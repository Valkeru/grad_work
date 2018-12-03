<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.10.18
 * Time: 18:50
 */

namespace App\EventSubscriber\Employee;

use App\Event\employee\EmployeeTokenInvalidateEvent;
use App\Service\SecurityService;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EmployeeEventSubscriber
 *
 * @package App\EventSubscriber\Employee
 */
class EmployeeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var SecurityService
     */
    private $securityService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * EmployeeEventSubscriber constructor.
     *
     * @param LoggerInterface $logger
     * @param SecurityService $securityService
     */
    public function __construct(LoggerInterface $logger, SecurityService $securityService)
    {
        $this->logger          = $logger;
        $this->securityService = $securityService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EmployeeTokenInvalidateEvent::NAME => 'onEmployeeTokenInvalidate'
        ];
    }

    /**
     * @param EmployeeTokenInvalidateEvent $event
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function onEmployeeTokenInvalidate(EmployeeTokenInvalidateEvent $event): void
    {
        $this->logger->info(sprintf('Invalidate token for employee %s', $event->getEmployee()));
        $this->securityService->blacklistEmployeeToken($event->getEmployee(), $event->getTokenString());
    }
}
