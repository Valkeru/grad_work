<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 27.05.18
 * Time: 23:27
 */

namespace App\Controller\PublicApi\v1;

use App\ApiMapper\CustomerMapper;
use App\Event\customer\CustomerRegistrationFinishedEvent;
use App\Service\SecurityService;
use Lcobucci\JWT\Token;
use App\Entity\Customer;
use App\Service\RegistrationService;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Valkeru\PublicApi\Registration\RegistrationRequest;
use Valkeru\PublicApi\Registration\RegistrationResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Exception\ValidatorException;
use Valkeru\PublicApi\Registration\RegistrationResponse_Error;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Valkeru\PublicApi\Registration\RegistrationResponse_Success;
use Valkeru\PublicApi\Registration\RegistrationResponse_Error_Code;

/**
 * Class RegistrationController
 *
 * @package App\Controller\PublicApi\v1
 *
 * @Security("request.server.get('PUBLIC_ENABLED') === '1'", message="Under maintenance. Please try later")
 * @Route("/register")
 */
class RegisterController extends Controller
{
    /**
     * @var RegistrationService
     */
    private $registrationService;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var RecursiveValidator
     */
    private $validator;

    public function __construct(RegistrationService $registrationService,
                                EventDispatcherInterface $dispatcher,
                                ValidatorInterface $validator
    )
    {
        $this->registrationService = $registrationService;
        $this->dispatcher          = $dispatcher;
        $this->validator           = $validator;
    }

    /**
     * @Route(methods={"POST"})
     * @param RegistrationRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function actionIndex(RegistrationRequest $request): Response
    {
        $response          = new RegistrationResponse();
        $registrationError = $this->validateRequest($request, $response);

        if ($registrationError->getCode() !== RegistrationResponse_Error_Code::_) {
            return JsonResponse::fromJsonString($response->serializeToJsonString());
        }

        try {
            /**
             * @var Customer $customer
             * @var Token    $token
             */
            [$customer, $token] = $this->registrationService->registerCustomer($request);

            $registrationSuccess = (new RegistrationResponse_Success())->setCustomer(CustomerMapper::mapCustomer($customer))
                ->setToken((string)$token);

            $event = new CustomerRegistrationFinishedEvent($customer);
            $this->dispatcher->dispatch($event::NAME, $event);

            return JsonResponse::fromJsonString($registrationSuccess->serializeToJsonString(), Response::HTTP_CREATED);
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::USER_ALREADY_EXISTS)
                    ->setMessage(sprintf('Customer %s already exists', $request->getLogin()))
            );

            return JsonResponse::fromJsonString($response->serializeToJsonString());
        } catch (ValidatorException $validatorException) {
            $message = json_decode($validatorException->getMessage());
            if (\count(get_object_vars($message)) !== 0) {
                return new JsonResponse($message);
            }

            return new Response(NULL, Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param RegistrationRequest  $request
     * @param RegistrationResponse $response
     *
     * @return RegistrationResponse_Error
     *
     * @throws NumberParseException
     */
    private function validateRequest(RegistrationRequest $request, RegistrationResponse $response): RegistrationResponse_Error
    {
        $registrationError     = new RegistrationResponse_Error();
        $phoneNumberUtil       = PhoneNumberUtil::getInstance();
        $emailConstraint       = new Assert\Email();
        $emailConstraint->mode = $emailConstraint::VALIDATION_MODE_HTML5;

        if ($request->getName() === '') {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::NAME_IS_BLANK)
                    ->setMessage('Name is not set')
            );
        } elseif ($request->getSurname() === '') {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::SURNAME_IS_BLANK)
                    ->setMessage('Surname is not set')
            );
        } elseif ($request->getLogin() === '') {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::LOGIN_IS_BLANK)
                    ->setMessage('Login is not set')
            );
        } elseif ($request->getEmail() === '') {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::EMAIL_IS_BLANK)
                    ->setMessage('Email is not set')
            );
        } elseif (\count($this->validator->validate($request->getEmail(), $emailConstraint)) > 0) {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::INVALID_EMAIL)
                    ->setMessage('Email is invalid')
            );
        } elseif ($request->getPhone() === '') {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::PHONE_IS_BLANK)
                    ->setMessage('Phone is not set')
            );
        } elseif (!$phoneNumberUtil->isValidNumber($phoneNumberUtil->parse($request->getPhone()))) {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::INVALID_PHONE)
                    ->setMessage('Phone number is invalid')
            );
        } elseif (($password = $request->getPassword()) === '') {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::PASSWORD_IS_BLANK)
                    ->setMessage('Password is not set')
            );
        } elseif (!SecurityService::validateCustomerPassword($password)) {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::INVALID_PASSWORD)
                    ->setMessage('Password should has length at least 8 symbols and consist of symbols A-z, 0-9, {, }, /, |, \, _, (, ), &, %, #, ^, -, +, =,')
            );
        }

        return $registrationError;
    }
}
