<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 13:22
 */

namespace App\ApiMapper;

use App\Entity\Customer;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
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
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $customerMessage->setId($customer->getId())
            ->setLogin($customer->getLogin())
            ->setName($customer->getName())
            ->setEmail($customer->getEmail())
            ->setPhone($phoneNumberUtil->format($customer->getPhone(), PhoneNumberFormat::E164))
            ->setIsBlocked($customer->getAccountStatus()->isBlocked());

        return $customerMessage;
    }
}
