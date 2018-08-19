<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 13:22
 */

namespace App\ApiMapper;

use App\Entity\Customer;
use Valkeru\PublicApi\Structures\Customer as CustomerMessage;

class CustomerMapper
{
    /**
     * @param Customer $customer
     *
     * @return CustomerMessage
     */
    public static function mapCustomer(Customer $customer): CustomerMessage
    {
        $customerMessage = new CustomerMessage();
        $customerMessage->setId($customer->getId())
            ->setLogin($customer->getLogin())
            ->setName($customer->getName())
            ->setEmail($customer->getEmail())
            ->setPhone($customer->getPhone())
            ->setIsBlocked($customer->getAccountStatus()->isBlocked());

        return $customerMessage;
    }
}
