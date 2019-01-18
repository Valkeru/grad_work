<?php

namespace App\Controller\PrivateApi;

use App\ApiMapper\Internal\SiteMapper;
use App\Entity\Customer;
use App\Entity\Site;
use App\Service\SiteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PrivateApi\Site\AddSiteRequest;
use Valkeru\PrivateApi\Site\AddSiteResponse;
use Valkeru\PrivateApi\Site\AddSiteResponse_Success;
use Valkeru\PrivateApi\Site\DeleteSiteRequest;

class SiteController extends Controller
{
    private $siteService;

    private $entityManager;

    public function __construct(SiteService $siteService, EntityManagerInterface $entityManager)
    {
        $this->siteService = $siteService;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(methods={"PUT"})
     */
    public function actionAddSite(AddSiteRequest $request)
    {
        $response = new AddSiteResponse();
        $customer = $this->entityManager->getRepository(Customer::class)
            ->findById($request->getCustId())->strict()->one();

        $site = $this->siteService->createSite($customer, $request->getPath());

        $response->setSuccess(
            (new AddSiteResponse_Success)
            ->setSite(SiteMapper::mapSite($site))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"DELETE"})
     * @param DeleteSiteRequest $request
     *
     * @return Response
     */
    public function actionDeleteSite(DeleteSiteRequest $request): Response
    {
        $site = $this->getDoctrine()->getRepository(Site::class)
            ->findById($request->getId())->strict()->one();

        $this->siteService->deleteSite($site);

        return new Response(NULL, Response::HTTP_NO_CONTENT);
    }
}
