<?php

namespace App\Controller\PrivateApi;

use App\ApiMapper\Internal\CustomerMapper;
use App\Entity\Customer;
use App\Service\RegistrationService;
use App\Service\SecurityService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Valkeru\PrivateApi\Custromer\EditCustomerRequest;
use Valkeru\PrivateApi\Custromer\NewCustomerRequest;
use Valkeru\PrivateApi\Custromer\NewCustomerResponse;
use Valkeru\PrivateApi\Custromer\NewCustomerResponse_Error;
use Valkeru\PrivateApi\Custromer\NewCustomerResponse_Error_Code;
use Valkeru\PrivateApi\Custromer\NewCustomerResponse_Success;
use Valkeru\PrivateApi\Custromer\SwitchStateRequest;
use Symfony\Component\Validator\Constraints as Assert;
use Valkeru\PrivateApi\Custromer\SwitchStateResponse;
use Valkeru\PrivateApi\Custromer\SwitchStateResponse_Success;
use Valkeru\PublicApi\Registration\RegistrationResponse_Error_Code;

/**
 * Class CustomerController
 *
 * @package App\Controller\PrivateApi
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/customer")
 */
class CustomerController extends Controller
{
    private $registrationService;

    /**
     * @var RecursiveValidator
     */
    private $validator;

    /**
     * CustomerController constructor.
     *
     * @param ValidatorInterface $validator
     */
    public function __construct(RegistrationService $registrationService, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->registrationService = $registrationService;
    }

    /**
     * @Route(methods={"PUT"})
     * @param NewCustomerRequest $request
     *
     * @return JsonResponse|Response
     * @throws NumberParseException
     * @throws \Exception
     */
    public function actionRegister(NewCustomerRequest $request): Response
    {
        $response = new NewCustomerResponse();
        $registrationError = $this->validateRequest($request, $response);

        if ($registrationError->getCode() !== NewCustomerResponse_Error_Code::_) {
            return JsonResponse::fromJsonString($response->serializeToJsonString());
        }

        try {
            [$customer, ] = $this->registrationService->registerCustomer($request, true);

            $success = (new NewCustomerResponse_Success())->setCustomer(CustomerMapper::mapCustomer($customer));

            return JsonResponse::fromJsonString($success->serializeToJsonString());
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

            return new Response(NULL, Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"POST"})
     * @param SwitchStateRequest $request
     *
     * @return JsonResponse
     */
    public function actionSwitchState(SwitchStateRequest $request): JsonResponse
    {
        $response = new SwitchStateResponse();

        $customer = $this->getDoctrine()->getRepository(Customer::class)
            ->findById($request->getId())->strict()->one();

        if (!$customer->getAccountStatus()->isBlocked()) {
            $customer->getAccountStatus()->setIsBlocked(true);
        } else {
            $customer->getAccountStatus()->setIsBlocked(false);
        }

        $response->setSuccess(
            (new SwitchStateResponse_Success())->setCustomer(CustomerMapper::mapCustomer($customer))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @param NewCustomerRequest  $request
     * @param NewCustomerResponse $response
     *
     * @return NewCustomerResponse_Error
     *
     * @throws NumberParseException
     */
    private function validateRequest(NewCustomerRequest $request, NewCustomerResponse $response): NewCustomerResponse_Error
    {
        $registrationError     = new NewCustomerResponse_Error();
        $phoneNumberUtil       = PhoneNumberUtil::getInstance();
        $emailConstraint       = new Assert\Email();
        $emailConstraint->mode = Assert\Email::VALIDATION_MODE_HTML5;

        if ($request->getName() === '') {
            $response->setError(
                $registrationError->setCode(NewCustomerResponse_Error_Code::NAME_NOT_SET)
                    ->setMessage('Name is not set')
            );
        } elseif ($request->getSurname() === '') {
            $response->setError(
                $registrationError->setCode(NewCustomerResponse_Error_Code::SURNAME_NOT_SET)
                    ->setMessage('Surname is not set')
            );
        } elseif ($request->getLogin() === '') {
            $response->setError(
                $registrationError->setCode(NewCustomerResponse_Error_Code::INVALID_LOGIN)
                    ->setMessage('Login is not set')
            );
        } elseif ($request->getEmail() === '') {
            $response->setError(
                $registrationError->setCode(NewCustomerResponse_Error_Code::INVALID_EMAIL)
                    ->setMessage('Email is not set')
            );
        } elseif (\count($this->validator->validate($request->getEmail(), $emailConstraint)) > 0) {
            $response->setError(
                $registrationError->setCode(NewCustomerResponse_Error_Code::INVALID_EMAIL)
                    ->setMessage('Email is invalid')
            );
        } elseif ($request->getPhone() === '') {
            $response->setError(
                $registrationError->setCode(NewCustomerResponse_Error_Code::INVALID_PHONE)
                    ->setMessage('Phone is not set')
            );
        } elseif (!$phoneNumberUtil->isValidNumber($phoneNumberUtil->parse($request->getPhone()))) {
            $response->setError(
                $registrationError->setCode(NewCustomerResponse_Error_Code::INVALID_PHONE)
                    ->setMessage('Phone number is invalid')
            );
        } elseif (($password = $request->getPassword()) === '') {
            $response->setError(
                $registrationError->setCode(NewCustomerResponse_Error_Code::INVALID_PASSWORD)
                    ->setMessage('Password is not set')
            );
        } elseif (!SecurityService::validatePassword($password)) {
            $response->setError(
                $registrationError->setCode(NewCustomerResponse_Error_Code::INVALID_PASSWORD)
                    ->setMessage('Password should has length at least 8 symbols and consist of symbols A-z, 0-9, {, }, /, |, \, _, (, ), &, %, #, ^, -, +, =,')
            );
        }

        return $registrationError;
    }
}
