<?php

namespace App\ApiMapper\Internal;

use App\Entity\Customer;
use Valkeru\PrivateApi\Structures\Customer as CustomerMessage;

class CustomerMapper
{
    public static function mapCustomer(Customer $customer): CustomerMessage
    {
        $customerMessage = new CustomerMessage();
        $customerMessage->setRegistrationDate(
            $customer->getAccountStatus()->getRegistrationDate()->format('Y-m-d')
            )
            ->setIsBlocked($customer->getAccountStatus()->isBlocked())
            ->setLogin($customer->getLogin())
            ->setName($customer->getName());

        return $customerMessage;
    }
}
