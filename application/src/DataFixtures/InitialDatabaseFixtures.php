<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 01.10.18
 * Time: 20:01
 */

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Server;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\DBAL\Types\{
    EmployeeDepartmentType, EmployeePositionType, EmployeeStatusType, ServerTypeType
};

/**
 * Class InitialDatabaseFixtures
 *
 * @package App\DataFixtures
 */
class InitialDatabaseFixtures extends Fixture
{
    private const DEFAULT_ROOT_PASSWORD = '2mx66ALa0DgoPOQs';

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        // Если стартовые данные в базу уже загружены - выходим
        if ($manager->getRepository(Employee::class)->find(1) !== NULL) {
            return;
        }

        $root           = new Employee();
        $rootServer     = new Server();
        $customerServer = new Server();

        $root->setName('Root Root')
            ->setDepartment(EmployeeDepartmentType::DEPARTMENT_DEV)
            ->setPosition(EmployeePositionType::POSITION_DEVELOPER)
            ->setStatus(EmployeeStatusType::STATUS_WORKING)
            ->setEmail('root@api.local')
            ->setLogin('root')
            ->setIsAdmin(true)
            ->setPassword(self::DEFAULT_ROOT_PASSWORD);

        $rootServer->setName('eos')
            ->setType(ServerTypeType::TYPE_SYSTEM)
            ->setInternalIp('172.16.1.1')
            ->setMainIp('0.0.0.0')
            ->setSslIp('0.0.0.0')
            ->setOutgoingIp('0.0.0.0');

        $customerServer->setName('jupiter')
            ->setInternalIp('172.16.3.1')
            ->setMainIp('0.0.0.0')
            ->setSslIp('0.0.0.0')
            ->setOutgoingIp('0.0.0.0')
            ->setRegistrationEnabled(true);

        $manager->persist($root);
        $manager->persist($rootServer);
        $manager->persist($customerServer);
        $manager->flush();
    }
}
