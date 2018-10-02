<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 17.09.18
 * Time: 3:44
 */

namespace App\Controller\PublicApi\v1;

use App\ApiMapper\DomainMapper;
use App\Entity\Customer;
use App\Entity\Domain;
use App\Repository\DomainRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PublicApi\Domain\{
    AddDomainRequest, AddDomainResponse, AddDomainResponse_Error, AddDomainResponse_Error_Code,
    AddDomainResponse_Success, DeleteDomainRequest, DeleteDomainResponse, DeleteDomainResponse_Success,
    DomainInfoRequest, DomainInfoResponse, DomainInfoResponse_Success, ListDomainsRequest,
    ListDomainsResponse, ListDomainsResponse_Success
};

/**
 * Class DomainController
 * @method Customer getUser()
 *
 * @package App\Controller\PublicApi\v1
 *
 * @Security("request.server.get('PUBLIC_ENABLED') === '1'", message="Under maintenance. Please try later")
 * @Security("has_role('ROLE_CUSTOMER')", message="Account is blocked, access denied")
 *
 * @Route("/domain")
 */
class DomainController extends Controller
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
    }

    /**
     * @Route(methods={"PUT"})
     * @param AddDomainRequest $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    public function actionAddDomain(AddDomainRequest $request): JsonResponse
    {
        $response = new AddDomainResponse();
        $domain   = new Domain();
        $domain->setFqdn($request->getFqdn())->setCustomer($this->getUser());

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->persist($domain);
            $this->entityManager->flush();

            $this->entityManager->commit();

            $response->setSuccess(
                (new AddDomainResponse_Success())->setDomain(DomainMapper::mapDomain($domain))
            );
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            $this->entityManager->rollback();
            $this->logger->warning($uniqueConstraintViolationException);

            $response->setError(
                (new AddDomainResponse_Error())
                    ->setCode(AddDomainResponse_Error_Code::DOMAIN_ALREADY_EXISTS)
                    ->setMessage('Domain already exists')
            );
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            $this->logger->error($exception);

            throw $exception;
        }

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/list", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function actionList(): JsonResponse
    {
        $response = new ListDomainsResponse();
        /** @var DomainRepository $repository */
        $repository = $this->entityManager->getRepository(Domain::class);

        $domainList    = $repository->findByCustomer($this->getUser())->all();
        $apiDomainList = [];

        foreach ($domainList as $domain) {
            $apiDomainList[] = DomainMapper::mapDomain($domain);
        }

        $response->setSuccess(
            (new ListDomainsResponse_Success())
                ->setDomains($apiDomainList)
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"GET"})
     * @param DomainInfoRequest $domainInfoRequest
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function actionDomainInfo(DomainInfoRequest $domainInfoRequest): JsonResponse
    {
        $response = new DomainInfoResponse();
        /** @var DomainRepository $repository */
        $repository = $this->entityManager->getRepository(Domain::class);
        $domain     = $repository->findById($domainInfoRequest->getId())
            ->findByCustomer($this->getUser())->strict()->one();

        $response->setSuccess(
            (new DomainInfoResponse_Success())->setDomain(DomainMapper::mapDomain($domain))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"DELETE"})
     * @param DeleteDomainRequest $request
     *
     * @return JsonResponse
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function actionDelete(DeleteDomainRequest $request): JsonResponse
    {
        $response = new DeleteDomainResponse();

        /** @var DomainRepository $repository */
        $repository = $this->entityManager->getRepository(Domain::class);
        $domain     = $repository->findById($request->getId())
            ->findByCustomer($this->getUser())->strict()->one();

        $this->entityManager->remove($domain);
        $this->entityManager->flush();

        $response->setSuccess(new DeleteDomainResponse_Success());

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }
}
