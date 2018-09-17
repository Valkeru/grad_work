<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 17:32
 */

namespace App\EventSubscriber\Customer;

use App\Event\customer\CustomerTokenInvalidateEvent;
use App\Service\SecurityService;
use Psr\Log\LoggerInterface;
use App\Event\customer\CustomerRegistrationFinishedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CustomerEventSubscriber
 *
 * @package App\EventSubscriber\Customer
 */
class CustomerEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SecurityService
     */
    private $securityService;

    /**
     * CustomerEventSubscriber constructor.
     *
     * @param LoggerInterface $logger
     * @param SecurityService $securityService
     */
    public function __construct(LoggerInterface $logger, SecurityService $securityService)
    {
        $this->logger = $logger;
        $this->securityService = $securityService;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CustomerRegistrationFinishedEvent::NAME => 'onCustomerRegistrationFinished',
            CustomerTokenInvalidateEvent::NAME      => 'onCustomerTokenInvalidate',
        ];
    }

    /**
     * @param CustomerRegistrationFinishedEvent $event
     */
    public function onCustomerRegistrationFinished(CustomerRegistrationFinishedEvent $event): void
    {
        $this->logger->info(sprintf('Customer %s successfully registered', $event->getCustomer()));
    }

    /**
     * @param CustomerTokenInvalidateEvent $event
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function onCustomerTokenInvalidate(CustomerTokenInvalidateEvent $event): void
    {
        $this->securityService->blacklistCustomerToken($event->getCustomer(), $event->getTokenString());
    }
}
