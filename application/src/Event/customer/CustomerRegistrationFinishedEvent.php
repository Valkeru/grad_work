<?php

namespace App\Event\customer;

use App\Entity\Customer;
use Symfony\Component\EventDispatcher\Event;

class CustomerRegistrationFinishedEvent extends Event
{
    public const NAME = 'customer.registration_finished';

    /**
     * @var Customer
     */
    private $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}
