<?php
namespace App\ApiMapper\Internal;

use App\Entity\Domain;
use App\Entity\Site;
use Valkeru\PrivateApi\Structures\Site as ApiSite;

class SiteMapper
{
    public static function mapSite(Site $site): ApiSite
    {
        $apiSite = new ApiSite();

        $apiDomains = [];
        $apiSite->setId($site->getId())
            ->setPath($site->getPath())
            ->setCustomer(CustomerMapper::mapCustomer($site->getCustomer()))
            ->setDomains(
                \call_user_func(function (Site $site) use ($apiDomains) {
                    /** @var Domain $domain */
                    foreach ($site->getDomains() as $domain) {
                        $apiDomains[] = $domain->getFqdn();
                    }

                    return $apiDomains;
                }, $site)
            );

        return $apiSite;
    }
}
