<?php

namespace App\Controller\PrivateApi;

use App\ApiMapper\Internal\MailboxMapper;
use App\Entity\Customer;
use App\Entity\Domain;
use App\Entity\Mailbox;
use App\Service\MailService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PrivateApi\Mail\AddMailboxRequest;
use Valkeru\PrivateApi\Mail\AddMailboxResponse;
use Valkeru\PrivateApi\Mail\AddMailboxResponse_Error;
use Valkeru\PrivateApi\Mail\AddMailboxResponse_Error_Code;
use Valkeru\PrivateApi\Mail\AddMailboxResponse_Success;
use Valkeru\PrivateApi\Mail\DeleteMailboxRequest;

/**
 * Class MailController
 *
 * @package App\Controller\PrivateApi
 * @Route("/mail")
 * @Security("has_role('ROLE_EMPLOYEE')")
 */
class MailController extends Controller
{
    private $mailService;

    private $entityManager;

    public function __construct(MailService $mailService, EntityManagerInterface $entityManager)
    {
        $this->mailService = $mailService;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(methods={"PUT"})
     * @param AddMailboxRequest $request
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function actionAddMailbox(AddMailboxRequest $request): JsonResponse
    {
        $response = new AddMailboxResponse();

        $domain = $this->entityManager->getRepository(Domain::class)
            ->findById($request->getDomainId())->strict()->one();

        try {
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function actionDeleteMailbox(DeleteMailboxRequest $request): Response
    {
        $mailbox = $this->entityManager->getRepository(Mailbox::class)
            ->findById($request->getId())->strict()->one();

        $this->mailService->deleteMailbox($mailbox);

        return new Response(NULL, Response::HTTP_NO_CONTENT);
    }
}
