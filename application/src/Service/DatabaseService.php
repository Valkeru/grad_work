<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Database;
use App\Entity\DatabaseAccess;
use App\Repository\DatabaseAccessRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class DatabaseService
 *
 * @package App\Service
 */
class DatabaseService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var DatabaseAccessRepository
     */
    private $databaseAccessRepository;

    /**
     * DatabaseService constructor.
     *
     * @param EntityManagerInterface   $entityManager
     * @param LoggerInterface          $logger
     * @param DatabaseAccessRepository $databaseAccessRepository
     */
    public function __construct(EntityManagerInterface $entityManager,
                                LoggerInterface $logger,
                                DatabaseAccessRepository $databaseAccessRepository)
    {
        $this->entityManager            = $entityManager;
        $this->logger                   = $logger;
        $this->databaseAccessRepository = $databaseAccessRepository;
    }

    /**
     * @param Customer $customer
     * @param string   $dbSuffix
     *
     * @return Database
     * @throws UniqueConstraintViolationException
     * @throws ORMException
     */
    public function createDatabase(Customer $customer, string $dbSuffix): Database
    {
        $database = new Database();
        $database->setCustomer($customer)->setSuffix($dbSuffix);

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->persist($database);
            $this->entityManager->flush();
            $this->entityManager->commit();

            $this->entityManager->refresh($database);
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            $this->entityManager->rollback();
            $this->logger->error($uniqueConstraintViolationException);

            throw $uniqueConstraintViolationException;
        } catch (ORMException $ORMException) {
            $this->entityManager->rollback();
            $this->logger->error($ORMException);

            throw $ORMException;
        }

        return $database;
    }

    /**
     * @param Database $database
     */
    public function dropDatabase(Database $database): void
    {
        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->remove($database);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (ORMException $e) {
            $this->entityManager->rollback();
            $this->logger->error($e);
        }
    }

    /**
     * @param Database $database
     * @param string   $host
     *
     * @return DatabaseAccess
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws UniqueConstraintViolationException
     */
    public function addDatabaseAccess(Database $database, string $host): DatabaseAccess
    {
        $newAccess = new DatabaseAccess();
        $newAccess->setDatabase($database)->setHost($host);

        $this->entityManager->persist($newAccess);
        $this->entityManager->flush();

        $this->entityManager->refresh($newAccess);

        return $newAccess;
    }

    /**
     * @param Database $database
     * @param string   $host
     *
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function dropDatabaseAccess(Database $database, string $host): void
    {
        $access = $this->databaseAccessRepository->findByDatabase($database)
            ->findByHost($host)->strict()->one();

        $this->entityManager->remove($access);
        $this->entityManager->flush();
    }
}
