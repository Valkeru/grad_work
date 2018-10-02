<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 15:20
 */

namespace App\Controller\PublicApi\v1;

use App\ApiMapper\MailboxMapper;
use App\Entity\Customer;
use App\Repository\DomainRepository;
use App\Repository\MailboxRepository;
use App\Service\MailService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PublicApi\Mail\{
    AddMailboxRequest, AddMailboxResponse, AddMailboxResponse_Error, AddMailboxResponse_Error_Code,
    AddMailboxResponse_Success, DeleteMailboxRequest, MailboxInfoRequest, MailboxInfoResponse,
    MailboxInfoResponse_Success, MailboxListRequest, MailboxListResponse, MailboxListResponse_Success,
};

/**
 * Class MailController
 *
 * @package App\Controller\PublicApi\v1
 *
 * @method Customer getUser()
 *
 * @Route("/mail")
 *
 * @Security("request.server.get('PUBLIC_ENABLED') === '1'", message="Under maintenance. Please try later")
 * @Security("has_role('ROLE_CUSTOMER')", message="Account is blocked, access denied")
 */
class MailController extends Controller
{
    /**
     * @var MailboxRepository
     */
    private $mailboxRepository;

    /**
     * @var DomainRepository
     */
    private $domainRepository;

    /**
     * @var MailService
     */
    private $mailService;

    /**
     * MailController constructor.
     *
     * @param MailService       $mailService
     * @param MailboxRepository $mailboxRepository
     * @param DomainRepository  $domainRepository
     */
    public function __construct(MailService $mailService,
                                   MailboxRepository $mailboxRepository,
                                   DomainRepository $domainRepository)
    {
        $this->mailboxRepository = $mailboxRepository;
        $this->mailService       = $mailService;
        $this->domainRepository  = $domainRepository;
    }

    /**
     * @Route(methods={"POST"})
     * @param MailboxListRequest $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function actionMailboxList(MailboxListRequest $request): JsonResponse
    {
        $response  = new MailboxListResponse();
        if (($domainId = $request->getDomainId()) === 0) {
            $mailboxes = $this->mailboxRepository->findByCustomer($this->getUser())->all();
        } else {
            $domain = $this->domainRepository->findByCustomer($this->getUser())
                ->findById($domainId)->strict()->one();
            $mailboxes = $this->mailboxRepository->findByCustomer($this->getUser())
                ->findByDomain($domain)->all();
        }

        $response->setSuccess(
            (new MailboxListResponse_Success())
                ->setMailboxes(\call_user_func(function (array $mailboxes) {
                    $apiMailboxes = [];

                    foreach ($mailboxes as $mailbox) {
                        $apiMailboxes[] = MailboxMapper::mapMailbox($mailbox);
                    }

                    return $apiMailboxes;
                }, $mailboxes))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route(methods={"PUT"})
     * @param AddMailboxRequest $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function actionAddMailbox(AddMailboxRequest $request): JsonResponse
    {
        $response = new AddMailboxResponse();
        try {
            $domain = $this->domainRepository->findByCustomer($this->getUser())
                ->findById($request->getDomainId())->strict()->one();

            $mailbox = $this->mailService->createMailbox($domain, $request->getName());
            $response->setSuccess(
                (new AddMailboxResponse_Success())
                ->setMailbox(MailboxMapper::mapMailbox($mailbox))
            );
        } catch (NotFoundHttpException $notFoundHttpException) {
            $response->setError(
                (new AddMailboxResponse_Error())
                    ->setCode(AddMailboxResponse_Error_Code::DOMAIN_NOT_FOUND)
                    ->setMessage('Domain not found')
            );
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            $response->setError(
                (new AddMailboxResponse_Error())
                    ->setCode(AddMailboxResponse_Error_Code::MAILBOX_ALREADY_EXISTS)
                    ->setMessage('Mailbox exists')
            );
        }

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"DELETE"})
     * @param DeleteMailboxRequest $request
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function actionDeleteMailbox(DeleteMailboxRequest $request): Response
    {
        $mailbox = $this->mailboxRepository->findById($request->getId())
            ->findByCustomer($this->getUser())->strict()->one();

        $this->mailService->deleteMailbox($mailbox);

        return new Response(NULL);
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"GET"})
     * @param MailboxInfoRequest $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function actionMailboxInfo(MailboxInfoRequest $request): JsonResponse
    {
        $response = new MailboxInfoResponse();

        $mailbox = $this->mailboxRepository->findById($request->getId())
            ->findByCustomer($this->getUser())->strict()->one();

        $response->setSuccess(
            (new MailboxInfoResponse_Success())
                ->setMailbox(MailboxMapper::mapMailbox($mailbox))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }
}
