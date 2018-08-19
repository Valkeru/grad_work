<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 27.05.18
 * Time: 23:28
 */

namespace App\Controller\PublicApi\v1;

use App\Entity\Customer;
use App\Event\customer\CustomerTokenInvalidateEvent;
use App\Helpers\BearerHelper;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Lcobucci\JWT\Parser;
use Predis\Client as Predis;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PublicApi\Auth\{
    LoginRequest,
    LoginResponse,
    LoginResponse_Error,
    LoginResponse_Error_Code,
    LoginResponse_Success,
    LogoutRequest,
    LogoutResponse,
    LogoutResponse_Success,
};

/**
 * Class AuthController
 *
 * @package App\Controller\PublicApi\v1
 *
 * @Route("/auth")
 */
class AuthController extends Controller
{
    /**
     * @var SecurityService
     */
    private $securityService;

    /**
     * @var EntityManager
     */
    private $entityManager;

    private $dispatcher;

    public function __construct(SecurityService $securityService,
                                EntityManagerInterface $entityManager,
                                EventDispatcherInterface $dispatcher)
    {
        $this->securityService = $securityService;
        $this->entityManager   = $entityManager;
        $this->dispatcher      = $dispatcher;
    }

    /**
     * @Route("/login", methods={"POST"})
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function actionLogin(LoginRequest $request): JsonResponse
    {
        $login    = $request->getLogin();
        $password = $request->getPassword();
        $response = new LoginResponse();

        if ($login === '' || $password === '') {
            $response->setError(
                (new LoginResponse_Error())->setCode(LoginResponse_Error_Code::EMPTY_CREDENTIALS)
                    ->setMessage('Login or password should not be empty')
            );

            return JsonResponse::fromJsonString($response->serializeToJsonString(), Response::HTTP_BAD_REQUEST);
        }

        try {
            $customer = Customer::find($this->entityManager)->findByLogin($login);
        } catch (NoResultException $exception) {
            throw new NotFoundHttpException();
        }

        if (!$customer->verifyPassword($password)) {
            $response->setError(
                (new LoginResponse_Error())->setCode(LoginResponse_Error_Code::INVALID_CREDENTIALS)
                    ->setMessage('Password is invalid')
            );

            return JsonResponse::fromJsonString($response->serializeToJsonString(), Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->securityService->authenticateCustomer($customer);
        $response->setSuccess(
            (new LoginResponse_Success())->setToken((string)$token)
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/logout", methods={"GET"})
     * @Security("has_role('ROLE_CUSTOMER_BLOCKED')")
     *
     * @param LogoutRequest $logoutRequest
     * @param Request       $request
     *
     * @return JsonResponse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function actionLogout(LogoutRequest $logoutRequest, Request $request): JsonResponse
    {
        $tokenString = BearerHelper::extractTokenString($request);
        /** @var Customer $customer */
        $customer = $this->getUser();

        $event = new CustomerTokenInvalidateEvent($customer, $tokenString);
        $this->dispatcher->dispatch($event::NAME, $event);

        $response = (new LogoutResponse())->setSuccess(
            (new LogoutResponse_Success())
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }
}
