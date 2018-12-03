<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 17.08.18
 * Time: 1:56
 */

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Employee;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use libphonenumber\PhoneNumberUtil;
use Valkeru\PrivateApi\Employee\CreateEmployeeRequest;
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

    /**
     * @var string
     */
    private $emailDomain;

    public function __construct(EntityManagerInterface $entityManager,
                                SecurityService $authenticationService,
                                ServerService $serverService,
                                string $emailDomain)
    {
        $this->entityManager         = $entityManager;
        $this->authenticationService = $authenticationService;
        $this->serverService         = $serverService;
        $this->emailDomain           = $emailDomain;
    }

    /**
     * @param RegistrationRequest $request
     *
     * @param bool                $isInternalRegistration
     *
     * @return array
     * @throws ORMException
     * @throws UniqueConstraintViolationException
     * @throws \libphonenumber\NumberParseException
     * @throws \Exception
     */
    public function registerCustomer(RegistrationRequest $request, bool $isInternalRegistration = false): array
    {
        $customer = (new Customer())
            ->setName(sprintf('%s %s', $request->getName(), $request->getSurname()))
            ->setLogin($request->getLogin())
            ->setPassword($request->getPassword())
            ->setEmail($request->getEmail())
            ->setPhone(PhoneNumberUtil::getInstance()->parse($request->getPhone()))
            ->setServer($this->serverService->selectServerForNewCustomer());

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();
            $this->entityManager->commit();
            $this->entityManager->refresh($customer);

            if (!$isInternalRegistration) {
                $token = $this->authenticationService->authenticateCustomer($customer);
            } else {
                $token = NULL;
            }

            return [$customer, $token];
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            $this->entityManager->rollback();
            throw $uniqueConstraintViolationException;
        } catch (ORMException $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }

    /**
     * @param CreateEmployeeRequest $request
     *
     * @return Employee
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function registerEmployee(CreateEmployeeRequest $request): Employee
    {
        $employee = (new Employee())
            ->setName("{$request->getName()} {$request->getSurname()}")
            ->setLogin($request->getLogin())
            ->setPassword($request->getPassword())
            ->setDepartment($request->getDepartment())
            ->setPosition($request->getPosition());

        if (($emailLogin = $request->getEmailLogin()) === '') {
            $employee->setEmail("{$request->getLogin()}@{$this->emailDomain}");
        } else {
            $employee->setEmail("{$emailLogin}@{$this->emailDomain}");
        }

        $this->entityManager->persist($employee);
        $this->entityManager->flush();
        $this->entityManager->refresh($employee);

        return $employee;
    }
}
