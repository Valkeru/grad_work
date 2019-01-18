<?php

namespace App\Controller\PrivateApi;

use App\Entity\Customer;
use App\Entity\Database;
use App\Service\DatabaseService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PrivateApi\Mysql\AddDatabaseRequest;
use Valkeru\PrivateApi\Mysql\AddDatabaseResponse;
use Valkeru\PrivateApi\Mysql\AddDatabaseResponse_Error;
use Valkeru\PrivateApi\Mysql\AddDatabaseResponse_Error_Code;
use Valkeru\PrivateApi\Mysql\DeleteDatabaseRequest;

/**
 * Class MysqlController
 *
 * @package App\Controller\PrivateApi
 * @Security("has_role('ROLE_EMPLOYEE')")
 * @Route("/mysql")
 */
class MysqlController extends Controller
{
    private $databaseService;

    private $entityManager;

    public function __construct(DatabaseService $databaseService, EntityManagerInterface $entityManager)
    {
        $this->databaseService = $databaseService;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(methods={"PUT"})
     * @param AddDatabaseRequest $request
     *
     * @return JsonResponse
     * @throws ORMException
     */
    public function actionCreate(AddDatabaseRequest $request): JsonResponse
    {
        $response = new AddDatabaseResponse();

        $customer = $this->entityManager->getRepository(Customer::class)
            ->findById($request->getCustId())->strict()->one();

        try {
            $this->databaseService->createDatabase($customer, $request->getSuffix());
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
     */
    public function actionDelete(DeleteDatabaseRequest $request): Response
    {
        $database = $this->entityManager->getRepository(Database::class)
            ->findById($request->getId())->strict()->one();

        $this->databaseService->dropDatabase($database);

        return new Response(NULL, Response::HTTP_NO_CONTENT);
    }
}
