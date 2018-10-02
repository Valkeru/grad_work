<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 15:13
 */

namespace App\Controller\PublicApi\v1;

use App\ApiMapper\SiteMapper;
use App\Entity\Customer;
use App\Entity\Domain;
use App\Entity\Site;
use App\Repository\SiteRepository;
use App\Service\SiteService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PublicApi\Site\{
    AddSiteRequest, AddSiteResponse, AddSiteResponse_Success, AttachDomainRequest, AttachDomainResponse,
    AttachDomainResponse_Success, DeleteSiteResponse, DetachDomainRequest, DetachDomainResponse,
    DetachDomainResponse_Success, SiteInfoRequest, SiteInfoResponse, SiteInfoResponse_Success, SiteListResponse,
    SiteListResponse_Success
};

/**
 * Class SiteController
 *
 * @package App\Controller\PublicApi\v1
 *
 * @method Customer getUser()
 *
 * @Route("/site")
 *
 * @Security("request.server.get('PUBLIC_ENABLED') === '1'", message="Under maintenance. Please try later")
 * @Security("has_role('ROLE_CUSTOMER')", message="Account is blocked, access denied")
 */
class SiteController extends Controller
{
    /**
     * @var SiteRepository
     */
    private $repository;

    /**
     * @var \App\Repository\DomainRepository
     */
    private $domainRepository;

    private $siteService;

    public function __construct(EntityManagerInterface $entityManager, SiteService $siteService)
    {
        $this->repository  = $entityManager->getRepository(Site::class);
        $this->domainRepository = $entityManager->getRepository(Domain::class);
        $this->siteService = $siteService;
    }

    /**
     * @Route(methods={"GET"})
     *
     * @return JsonResponse
     */
    public function actionList(): JsonResponse
    {
        $response = new SiteListResponse();

        $sites = $this->repository->findByCustomer($this->getUser())->all();

        if (!empty($sites)) {
            $apiSites = [];
            foreach ($sites as $site) {
                $apiSites[] = (new \Valkeru\PublicApi\Site\Site())->setId($site->getId())
                    ->setPath($site->getPath())
                    ->setDomains(\call_user_func(function (Site $site) {
                        $domains = [];
                        /** @var Domain $domain */
                        foreach ($site->getDomains() as $domain) {
                            $domains[] = (new \Valkeru\PublicApi\Site\Domain())->setId($domain->getId())
                                ->setFqdn($domain->getFqdn());
                        }

                        return $domains;
                    }, $site));
            }

            $response->setSuccess((new SiteListResponse_Success())->setSites($apiSites));
        } else {
            $response->setSuccess(new SiteListResponse_Success());
        }

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"GET"})
     *
     * @param SiteInfoRequest $request
     *
     * @return JsonResponse
     *
     * @throws NonUniqueResultException
     */
    public function actionInfo(SiteInfoRequest $request): JsonResponse
    {
        $response = new SiteInfoResponse();
        $site     = $this->repository->findByCustomer($this->getUser())
            ->findById($request->getId())->strict()->one();

        $response->setSuccess(
            (new SiteInfoResponse_Success())->setSite(SiteMapper::mapSite($site))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route(methods={"PUT"})
     *
     * @param AddSiteRequest $request
     *
     * @return JsonResponse
     */
    public function actionAdd(AddSiteRequest $request): JsonResponse
    {
        $response = new AddSiteResponse();

        $site = $this->siteService->createSite($this->getUser(), $request->getPath());

        $response->setSuccess(
            (new AddSiteResponse_Success)->setSite(SiteMapper::mapSite($site))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"DELETE"})
     *
     * @return JsonResponse
     */
    public function actionDelete(): JsonResponse
    {
        $response = new DeleteSiteResponse();

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}/attach-domain", requirements={"id": "\d+"}, methods={"POST"})
     * @return JsonResponse
     */
    public function actionAttachDomain(AttachDomainRequest $request): JsonResponse
    {
        $response = new AttachDomainResponse();

        $site = $this->repository->findById($request->getId())
            ->findByCustomer($this->getUser())->strict()->one();

        $domain = $this->domainRepository->findById($request->getDomainId())
            ->findByCustomer($this->getUser())->strict()->one();

        $this->siteService->attachDomain($site, $domain);

        $response->setSuccess(
            (new AttachDomainResponse_Success())->setSite(SiteMapper::mapSite($site))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}/detach-domain", requirements={"id": "\d+"}, methods={"POST"})
     *
     * @param DetachDomainRequest $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function actionDetachDomain(DetachDomainRequest $request): JsonResponse
    {
        $response = new DetachDomainResponse();

        $site = $this->repository->findById($request->getId())
            ->findByCustomer($this->getUser())->strict()->one();

        $domain = $this->domainRepository->findById($request->getDomainId())
            ->findByCustomer($this->getUser())->strict()->one();

        $this->siteService->detachDomain($site, $domain);

        $response->setSuccess(
            (new DetachDomainResponse_Success())->setSite(SiteMapper::mapSite($site))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }
}
