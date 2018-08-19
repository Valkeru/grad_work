<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 17.08.18
 * Time: 1:56
 */

namespace App\Service;

use App\Entity\Customer;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Valkeru\PublicApi\Registration\RegistrationRequest;
use Doctrine\ORM\{
    ORMException, EntityManager, EntityManagerInterface
};

class RegistrationService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SecurityService
     */
    private $authenticationService;

    /**
     * @var ServerService
     */
    private $serverService;

    public function __construct(EntityManagerInterface $entityManager, SecurityService $authenticationService, ServerService $serverService)
    {
        $this->entityManager         = $entityManager;
        $this->authenticationService = $authenticationService;
        $this->serverService = $serverService;
    }

    public function registerCustomer(RegistrationRequest $request): array
    {
        $customer = (new Customer())
            ->setName(sprintf('%s %s', $request->getName(), $request->getSurname()))
            ->setLogin($request->getLogin())
            ->setPassword($request->getPassword())
            ->setEmail($request->getEmail())
            ->setPhone($request->getPhone())
            ->setServer($this->serverService->selectServerForNewCustomer());

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();
            $this->entityManager->commit();
            $this->entityManager->refresh($customer);

            $token = $this->authenticationService->authenticateCustomer($customer);

            return [$customer, $token];
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            $this->entityManager->rollback();
            throw $uniqueConstraintViolationException;
        }
        catch (ORMException $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}
