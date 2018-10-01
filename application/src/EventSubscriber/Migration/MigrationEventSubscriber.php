<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 20.08.18
 * Time: 13:01
 */

namespace App\EventSubscriber\Migration;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Migrations\Event\MigrationsEventArgs;
use Doctrine\DBAL\Migrations\Events;
use Psr\Log\LoggerInterface;

/**
 * Class MigrationEventSubscriber
 *
 * @package App\EventSubscriber\Migration
 */
class MigrationEventSubscriber implements EventSubscriber
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MigrationEventSubscriber constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onMigrationsVersionExecuting,
            Events::onMigrationsVersionExecuted
        ];
    }

    /**
     * @param MigrationsEventArgs $args
     */
    public function onMigrationsVersionExecuting(MigrationsEventArgs $args): void
    {
        $this->logger->info(sprintf('Migration version %s started', $args->getConfiguration()->getNextVersion() ));
    }

    /**
     * @param MigrationsEventArgs $args
     */
    public function onMigrationsVersionExecuted(MigrationsEventArgs $args): void
    {
        $this->logger->info(sprintf('Migration version %s finished', $args->getConfiguration()->getCurrentVersion()));
    }
}
