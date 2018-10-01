<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 17.09.18
 * Time: 4:25
 */

namespace App\ApiMapper;

use App\Entity\Domain;
use Valkeru\PublicApi\Structures\Domain as DomainMessage;

class DomainMapper
{
    public static function mapDomain(Domain $domain): DomainMessage
    {
        $domainMessage = new DomainMessage();

        $domainMessage->setFqdn($domain->getFqdn())
            ->setId($domain->getId())
            ->setIsBlocked($domain->isBlocked());

        return $domainMessage;
    }
}
