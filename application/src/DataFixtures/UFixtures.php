<?php

namespace App\DataFixtures;

use App\DBAL\Types\EmployeeDepartmentType;
use App\DBAL\Types\EmployeePositionType;
use App\DBAL\Types\EmployeeStatusType;
use App\Entity\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UFixtures
 *
 * @package App\DataFixtures
 */
class UFixtures extends Fixture
{
    /**
     * @var \Doctrine\ORM\EntityManager|object
     */
    private $needleEm;

    /**
     * UFixtures constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->needleEm = $container->get('doctrine.orm.testing_entity_manager');
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        if ($this->needleEm !== $manager) {
            return;
        }

        $employee = new Employee();
        $employee->setLogin('admin')
            ->setEmail('admin@api.local')
            ->setName('Лис Накраман')
            ->setPassword('qwerty')
            ->setDepartment(EmployeeDepartmentType::DEPARTMENT_DEV)
            ->setPosition(EmployeePositionType::POSITION_DEVELOPER)
            ->setStatus(EmployeeStatusType::STATUS_WORKING)
            ->setIsAdmin(true);

        $manager->persist($employee);
        $manager->flush();
    }
}
