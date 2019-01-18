<?php

namespace App\Controller\PublicApi\v1;

use App\ApiMapper\MysqlDatabaseMapper;
use App\Entity\Customer;
use App\Repository\DatabaseRepository;
use App\Service\DatabaseService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PublicApi\Mysql\{
    AddDatabaseRequest, AddDatabaseResponse, AddDatabaseResponse_Error, AddDatabaseResponse_Error_Code,
    AddDatabaseResponse_Success, AddRemoteAccessRequest, AddRemoteAccessResponse, AddRemoteAccessResponse_Error,
    AddRemoteAccessResponse_Error_Code, AddRemoteAccessResponse_Success, DatabaseInfoRequest,
    DatabaseInfoResponse, DatabaseInfoResponse_Success, DatabaseListResponse, DatabaseListResponse_Success,
    DeleteDatabaseRequest, RemoveRemoteAccessRequest, RemoveRemoteAccessResponse, RemoveRemoteAccessResponse_Success,
};

/**
 * Class MysqlController
 *
 * @package App\Controller\PublicApi\v1
 * @method Customer getUser()
 *
 * @Route("/mysql")
 * @Security("request.server.get('PUBLIC_ENABLED') === '1'", message="Under maintenance. Please try later")
 * @Security("has_role('ROLE_CUSTOMER')", message="Account is blocked, access denied")
 */
class MysqlController extends Controller
{
    /**
     * @var DatabaseService
     */
    private $databaseService;

    /**
     * @var DatabaseRepository
     */
    private $databaseRepository;

    public function __construct(DatabaseService $databaseService, DatabaseRepository $databaseRepository)
    {
        $this->databaseService = $databaseService;
        $this->databaseRepository = $databaseRepository;
    }

    /**
     * @Route(methods={"GET"})
     * @return JsonResponse
     */
    public function actionList(): JsonResponse
    {
        $response = new DatabaseListResponse();

        $databases = $this->databaseRepository->finbByCustomer($this->getUser())->all();

        $response->setSuccess(
            (new DatabaseListResponse_Success())
            ->setDatabases(\call_user_func(function (array $dbList) {
                $databases = [];

                foreach ($dbList as $database) {
                    $databases[] = MysqlDatabaseMapper::mapDatabase($database);
                }

                return $databases;
            }, $databases))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"GET"})
     *
     * @param DatabaseInfoRequest $request
     *
     * @return JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function actionInfo(DatabaseInfoRequest $request): JsonResponse
    {
        $response = new DatabaseInfoResponse();
        $database = $this->databaseRepository->findById($request->getId())
            ->finbByCustomer($this->getUser())->strict()->one();

        $response->setSuccess(
            (new DatabaseInfoResponse_Success())->setDatabase(MysqlDatabaseMapper::mapDatabase($database))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route(methods={"PUT"})
     *
     * @param AddDatabaseRequest $request
     *
     * @return JsonResponse
     * @throws ORMException
     */
    public function actionCreate(AddDatabaseRequest $request): JsonResponse
    {
        $response = new AddDatabaseResponse();

        try {
            $database = $this->databaseService->createDatabase($this->getUser(), $request->getSuffix());

            $response->setSuccess(
                (new AddDatabaseResponse_Success())
                    ->setDatabase(MysqlDatabaseMapper::mapDatabase($database))
            );
        } catch (UniqueConstraintViolationException $e) {
            $response->setError(
                (new AddDatabaseResponse_Error())->setCode(AddDatabaseResponse_Error_Code::DATABASE_EXISTS)
                ->setMessage('This database already exists')
            );
        } catch (ORMException $e) {
            throw $e;
        }

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"}, methods={"DELETE"})
     * @param DeleteDatabaseRequest $request
     *
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function actionDelete(DeleteDatabaseRequest $request): Response
    {
        $database = $this->databaseRepository->findById($request->getId())->strict()->one();
        $this->databaseService->dropDatabase($database);

        return new Response(NULL);
    }

    /**
     * @Route("/access", methods={"PUT"})
     * @param AddRemoteAccessRequest $request
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function actionAddAccess(AddRemoteAccessRequest $request): JsonResponse
    {
        $response = new AddRemoteAccessResponse();
        $database = $this->databaseRepository->findById($request->getDatabaseId())
            ->finbByCustomer($this->getUser())->strict()->one();

        try {
            $this->databaseService->addDatabaseAccess($database, $request->getHost());

            $response->setSuccess(
                (new AddRemoteAccessResponse_Success())
                    ->setDatabase(MysqlDatabaseMapper::mapDatabase($database))
            );
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            $response->setError(
                (new AddRemoteAccessResponse_Error())
                ->setCode(AddRemoteAccessResponse_Error_Code::REMOTE_ACCESS_ALREADY_EXISTS)
                ->setMessage('Access exists')
            );
        }

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/access", methods={"DELETE"})
     * @param RemoveRemoteAccessRequest $request
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function actionRemoveAccess(RemoveRemoteAccessRequest $request): JsonResponse
    {
        $response = new RemoveRemoteAccessResponse();
        $database = $this->databaseRepository->findById($request->getDatabaseId())
            ->finbByCustomer($this->getUser())->strict()->one();

        $this->databaseService->dropDatabaseAccess($database, $request->getHost());

        $response->setSuccess(
            (new RemoveRemoteAccessResponse_Success())
            ->setDatabase(MysqlDatabaseMapper::mapDatabase($database))
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }
}
