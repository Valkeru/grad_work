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
use Lcobucci\JWT\Token;
use App\Entity\Customer;
use App\Service\RegistrationService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
 * @Route("/register")
 */
class RegisterController extends Controller
{
    private const PASSWORD_REGEX = 'A-z\d\{\}\/|\\_\(\)&%\#\^\-\+\=';

    /**
     * @var RegistrationService
     */
    private $registrationService;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(RegistrationService $registrationService, EventDispatcherInterface $dispatcher)
    {
        $this->registrationService = $registrationService;
        $this->dispatcher          = $dispatcher;
    }

    /**
     * @Route(methods={"POST"})
     * @param RegistrationRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function actionIndex(RegistrationRequest $request): JsonResponse
    {
        $response          = new RegistrationResponse();
        $registrationError = $this->validateRequest($request, $response);

        if ($registrationError->getCode() !== RegistrationResponse_Error_Code::_) {
            return JsonResponse::fromJsonString($response->serializeToJsonString(), Response::HTTP_BAD_REQUEST);
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

            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::INTERNAL_ERROR)
                    ->setMessage('Internal server error. Please contact support')
            );

            return JsonResponse::fromJsonString($response->serializeToJsonString());
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param RegistrationRequest  $request
     * @param RegistrationResponse $response
     *
     * @return RegistrationResponse_Error
     */
    private function validateRequest(RegistrationRequest $request, RegistrationResponse $response): RegistrationResponse_Error
    {
        $registrationError = new RegistrationResponse_Error();

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
        } elseif ($request->getPhone() === '') {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::PHONE_IS_BLANK)
                    ->setMessage('Phone is not set')
            );
        } elseif (($password = $request->getPassword()) === '') {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::PASSWORD_IS_BLANK)
                    ->setMessage('Password is not set')
            );
        } elseif (!\preg_match(sprintf('#[%s]{8,}#', self::PASSWORD_REGEX), $password)) {
            $response->setError(
                $registrationError->setCode(RegistrationResponse_Error_Code::INVALID_PASSWORD)
                    ->setMessage('Password should has length at least 8 symbols and consist of symbols A-z, 0-9, {, }, /, |, \, _, (, ), &, %, #, ^, -, +, =,')
            );
        }

        return $registrationError;
    }
}
