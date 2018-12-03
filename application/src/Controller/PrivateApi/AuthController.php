<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.10.18
 * Time: 17:07
 */

namespace App\Controller\PrivateApi;

use App\Entity\Employee;
use App\Event\employee\EmployeeTokenInvalidateEvent;
use App\Helpers\BearerHelper;
use App\Repository\EmployeeRepository;
use App\Service\SecurityService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PrivateApi\Auth\{
    LoginRequest, LoginResponse, LoginResponse_Error, LoginResponse_Error_Code, LoginResponse_Success,
};

/**
 * Class AuthController
 *
 * @package App\Controller\PrivateApi
 *
 * @method Employee getUser()
 * @Route("/auth")
 */
class AuthController extends Controller
{
    /**
     * @var SecurityService
     */
    private $securityService;

    /**
     * @var EmployeeRepository
     */
    private $employeeRepository;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * AuthController constructor.
     *
     * @param SecurityService          $securityService
     * @param EmployeeRepository       $employeeRepository
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(SecurityService $securityService,
                                EmployeeRepository $employeeRepository,
                                EventDispatcherInterface $dispatcher)
    {
        $this->securityService    = $securityService;
        $this->employeeRepository = $employeeRepository;
        $this->dispatcher         = $dispatcher;
    }

    /**
     * @Route("/login", methods={"POST"})
     * @param LoginRequest $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function actionLogin(LoginRequest $request): JsonResponse
    {
        $response = new LoginResponse();

        $login    = $request->getLogin();
        $password = $request->getPassword();

        if ($login === '' || $password === '') {
            $response->setError(
                (new LoginResponse_Error())->setCode(LoginResponse_Error_Code::EMPTY_CREDENTIALS)
                    ->setMessage('Login and password should not be empty')
            );

            return JsonResponse::fromJsonString($response->serializeToJsonString());
        }

        try {
            $employee = $this->employeeRepository->findByLogin($request->getLogin())->strict()->one();
        } catch (NotFoundHttpException $notFoundHttpException) {
            $response->setError(
                (new LoginResponse_Error())
                    ->setCode(LoginResponse_Error_Code::INVALID_LOGIN)
                    ->setMessage('Employee not found')
            );

            return JsonResponse::fromJsonString($response->serializeToJsonString());
        }

        if (!$employee->verifyPassword($password)) {
            $response->setError(
                (new LoginResponse_Error())->setCode(LoginResponse_Error_Code::INVALID_PASSWORD)
                    ->setMessage('Password is invalid')
            );

            return JsonResponse::fromJsonString($response->serializeToJsonString());
        }

        $token = $this->securityService->authenticateEmployee($employee);
        $response->setSuccess((new LoginResponse_Success())->setToken((string)$token));

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/logout", methods={"GET"})
     * @Security("has_role('ROLE_EMPLOYEE')")
     * @param Request $request
     *
     * @return Response
     */
    public function actionLogout(Request $request): Response
    {
        $token    = BearerHelper::extractTokenString($request);
        $employee = $this->getUser();

        $event = new EmployeeTokenInvalidateEvent($employee, $token);
        $this->dispatcher->dispatch($event::NAME, $event);

        return new Response(NULL);
    }
}
