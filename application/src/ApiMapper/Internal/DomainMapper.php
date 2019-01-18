<?php

namespace App\ApiMapper\Internal;

use App\Entity\Domain;
use Valkeru\PrivateApi\Structures\Domain as DomainMessage;

class DomainMapper
{
    public static function mapDomain(Domain $domain): DomainMessage
    {
        $domainMessage = new DomainMessage();

        $domainMessage->setFqdn($domain->getFqdn())
            ->setId($domain->getId())
            ->setIsBlocked($domain->isBlocked())
            ->setCustomer(CustomerMapper::mapCustomer($domain->getCustomer()));

        return $domainMessage;
    }
}
