<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 12.06.18
 * Time: 23:20
 */

namespace App\Controller\PrivateApi;

use App\ApiMapper\Internal\EmployeeMapper;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Service\RegistrationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PrivateApi\Employee\CreateEmployeeRequest;
use Valkeru\PrivateApi\Employee\CreateEmployeeResponse;
use Valkeru\PrivateApi\Employee\CreateEmployeeResponse_Success;
use Valkeru\PrivateApi\Employee\EmployeeInfoRequest;
use Valkeru\PrivateApi\Employee\EmployeeInfoResponse;
use Valkeru\PrivateApi\Employee\EmployeeInfoResponse_Success;

/**
 * Class EmployeeController
 *
 * @package App\Controller\PrivateApi
 *
 * @method Employee getUser()
 *
 * @Route("/employee")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EmployeeController extends Controller
{
    private $registrationService;

    private $employeeRepository;

    public function __construct(RegistrationService $registrationService, EmployeeRepository $repository)
    {
        $this->registrationService = $registrationService;
        $this->employeeRepository = $repository;
    }

    /**
     * @Route("", methods={"PUT"})
     *
     * @param CreateEmployeeRequest $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function newEmployeeAction(CreateEmployeeRequest $request): JsonResponse
    {
        $response = new CreateEmployeeResponse();
        $employee = $this->registrationService->registerEmployee($request);
        $response->setSuccess(
            (new CreateEmployeeResponse_Success())
                ->setEmployee(EmployeeMapper::mapEmployee($employee)));

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"PATCH"})
     *
     * @return Response
     */
    public function editEmployeeAction(): Response
    {
        return new Response();
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function blockEmployeeAction(): Response
    {
        return new Response();
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET"})
     *
     * @param EmployeeInfoRequest $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function employeeInfoAction(EmployeeInfoRequest $request): JsonResponse
    {
        $response = new EmployeeInfoResponse();

        $id = $request->getId();
        $employee = $this->employeeRepository->findById($id)->strict()->one();

        $response->setSuccess(
            (new EmployeeInfoResponse_Success())
            ->setEmployee(EmployeeMapper::mapEmployee($employee))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }
}
