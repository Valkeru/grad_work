<?php

namespace App\Event\customer;

use App\Entity\Customer;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CustomerTokenInvalidateEvent
 *
 * @package App\Event\customer
 */
class CustomerTokenInvalidateEvent extends Event
{
    public const NAME = 'customer.token_invalidate';

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var string
     */
    private $tokenString;

    /**
     * CustomerTokenInvalidateEvent constructor.
     *
     * @param Customer $customer
     * @param string   $tokenString
     */
    public function __construct(Customer $customer, string $tokenString)
    {
        $this->customer    = $customer;
        $this->tokenString = $tokenString;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @return string
     */
    public function getTokenString(): string
    {
        return $this->tokenString;
    }
}
