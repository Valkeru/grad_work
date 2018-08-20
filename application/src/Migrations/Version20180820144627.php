<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Customer;
use App\Entity\Employee;
use App\Entity\Server;
use Doctrine\DBAL\Migrations\IrreversibleMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Подготовка системы к работе на пустой БД: создание администратора, серверов и демо-юзера
 */
final class Version20180820144627 extends AbstractMigration implements ContainerAwareInterface
{
    private const DEFAULT_ROOT_PASSWORD      = '2mx66ALa0DgoPOQs';
    private const DEFAULT_DEMO_USER_PASSWORD = 'HlwtxwngEO5vQe8M';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null): void
    {
        if ($container !== NULL) {
            $this->em = $container->get('doctrine.orm.entity_manager');
        }
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $root           = new Employee();
        $customer       = new Customer();
        $rootServer     = new Server();
        $customerServer = new Server();

        $root->setName('Root Root')
            ->setDepartment($root::DEPARTMENT_DEV)
            ->setPosition($root::POSITION_DEVELOPER)
            ->setStatus($root::STATUS_WORKING)
            ->setEmail('root@api.local')
            ->setLogin('root')
            ->setIsAdmin(true)
            ->setPassword(self::DEFAULT_ROOT_PASSWORD);

        $rootServer->setName('eos')
            ->setType($rootServer::TYPE_SYSTEM)
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

        $customer->setServer($customerServer)
            ->setName('John Doe')
            ->setLogin('demo')
            ->setEmail('demo@example.com')
            ->setPassword(self::DEFAULT_DEMO_USER_PASSWORD)
            ->setPhone('+71234567890');

        $this->em->persist($root);
        $this->em->persist($rootServer);
        $this->em->persist($customer);
        $this->em->flush();
    }

    /**
     * @param Schema $schema
     *
     * @throws IrreversibleMigrationException
     */
    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
