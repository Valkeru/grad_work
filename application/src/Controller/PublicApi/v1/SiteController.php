<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 15:13
 */

namespace App\Controller\PublicApi\v1;

use App\Entity\Domain;
use App\Entity\Site;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PublicApi\Site\AddSiteResponse;
use Valkeru\PublicApi\Site\AttachDomainResponse;
use Valkeru\PublicApi\Site\DeleteSiteResponse;
use Valkeru\PublicApi\Site\DetachDomainResponse;
use Valkeru\PublicApi\Site\SiteInfoResponse;
use Valkeru\PublicApi\Site\SiteListResponse;
use Valkeru\PublicApi\Site\SiteListResponse_Success;

/**
 * Class SiteController
 *
 * @package App\Controller\PublicApi\v1
 * @Route("/site")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class SiteController extends Controller
{
    /**
     * @var SiteRepository
     */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Site::class);
    }

    /**
     * @Route(methods={"GET"})
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
        }

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"GET"})
     * @return JsonResponse
     */
    public function actionInfo(): JsonResponse
    {
        $response = new SiteInfoResponse();

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route(methods={"PUT"})
     * @return JsonResponse
     */
    public function actionAdd(): JsonResponse
    {
        $response = new AddSiteResponse();

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"DELETE"})
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
    public function actionAttachDomain(): JsonResponse
    {
        $response = new AttachDomainResponse();

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}/detach-domain", requirements={"id": "\d+"}, methods={"POST"})
     * @return JsonResponse
     */
    public function actionDetachDomain(): JsonResponse
    {
        $response = new DetachDomainResponse();

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }
}
