<?php

namespace App\Controller\PrivateApi;

use App\Entity\Customer;
use App\Entity\Employee;
use App\Service\SecurityService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PrivateApi\Security\CustomerTokenRequest;
use Valkeru\PrivateApi\Security\CustomerTokenResponse;
use Valkeru\PublicApi\Security\ChangePasswordRequest;

/**
 * Class SecirityController
 * @method Employee getUser()
 *
 * @package App\Controller\PrivateApi
 * @Security("has_role('ROLE_EMPLOYEE')")
 * @Route("/security")
 */
class SecirityController extends Controller
{
    private $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }
    /**
     * @Route("/password", methods={"PATCH"})
     */
    public function actionChangePassword(ChangePasswordRequest $request)
    {

    }

    /**
     * @Route("/customer-token/{id}", requirements={"id": "\d+"})
     * @param CustomerTokenRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function actionGetCustomerToken(CustomerTokenRequest $request): JsonResponse
    {
        $response = new CustomerTokenResponse();

        $customer = $this->getDoctrine()->getRepository(Customer::class)
            ->findById($request->getId())->strict()->one();
        $token = $this->securityService->authenticateCustomer($customer, $this->getUser());

        $response->setToken((string)$token);

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }
}
