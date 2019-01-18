<?php

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
use Valkeru\PrivateApi\Employee\BlockEmployeeRequest;
use Valkeru\PrivateApi\Employee\BlockEmployeeResponse;
use Valkeru\PrivateApi\Employee\BlockEmployeeResponse_Code;
use Valkeru\PrivateApi\Employee\BlockEmployeeResponse_Error;
use Valkeru\PrivateApi\Employee\BlockEmployeeResponse_Error_Code;
use Valkeru\PrivateApi\Employee\BlockEmployeeResponse_Success;
use Valkeru\PrivateApi\Employee\CreateEmployeeRequest;
use Valkeru\PrivateApi\Employee\CreateEmployeeResponse;
use Valkeru\PrivateApi\Employee\CreateEmployeeResponse_Success;
use Valkeru\PrivateApi\Employee\EmployeeInfoRequest;
use Valkeru\PrivateApi\Employee\EmployeeInfoResponse;
use Valkeru\PrivateApi\Employee\EmployeeInfoResponse_Success;
use Valkeru\PrivateApi\Employee\UnblockEmployeeResponse;
use Valkeru\PrivateApi\Employee\UnblockEmployeeResponse_Code;
use Valkeru\PrivateApi\Employee\UnblockEmployeeResponse_Error;
use Valkeru\PrivateApi\Employee\UnblockEmployeeResponse_Error_Code;
use Valkeru\PrivateApi\Employee\UnblockEmployeeResponse_Success;

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
     * @Route("/{id}/block", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @param BlockEmployeeRequest $request
     *
     * @return JsonResponse
     */
    public function blockEmployeeAction(BlockEmployeeRequest $request): JsonResponse
    {
        $response = new BlockEmployeeResponse();

        $employee = $this->employeeRepository
            ->findById($request->getId())->strict()->one();

        if ($employee->isBlocked()) {
            $response->setError(
                (new BlockEmployeeResponse_Error())
                ->setCode(BlockEmployeeResponse_Error_Code::ALREADY_BLOCKED)
                ->setMessage(sprintf('Employee %s already blocked', $employee))
            );
        } else {
            $employee->setIsBlocked(true);
            $this->getDoctrine()->getManager()->persist($employee);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getManager()->refresh($employee);

            $response->setSuccess(
                (new BlockEmployeeResponse_Success())
                    ->setEmployee(EmployeeMapper::mapEmployee($employee))
            );
        }

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}/unblock", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @param BlockEmployeeRequest $request
     *
     * @return JsonResponse
     */
    public function unblockEmployeeAction(BlockEmployeeRequest $request): JsonResponse
    {
        $response = new UnblockEmployeeResponse();

        $employee = $this->employeeRepository
            ->findById($request->getId())->strict()->one();

        if (!$employee->isBlocked()) {
            $response->setError(
                (new UnblockEmployeeResponse_Error())
                    ->setCode(UnblockEmployeeResponse_Error_Code::NOT_BLOCKED)
                    ->setMessage(sprintf('Employee %s is not blocked', $employee))
            );
        } else {
            $employee->setIsBlocked(false);
            $this->getDoctrine()->getManager()->persist($employee);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getManager()->refresh($employee);

            $response->setSuccess(
                (new UnblockEmployeeResponse_Success())
                    ->setEmployee(EmployeeMapper::mapEmployee($employee))
            );
        }

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET"})
     *
     * @param EmployeeInfoRequest $request
     *
     * @return JsonResponse
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
