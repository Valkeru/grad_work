<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 01.10.18
 * Time: 11:15
 */

namespace App\ApiMapper;

use App\Entity\Domain;
use App\Entity\Site;
use Valkeru\PublicApi\Structures\Site as ApiSite;

class SiteMapper
{
    public static function mapSite(Site $site): ApiSite
    {
        $apiSite = new ApiSite();

        $apiDomains = [];
        $apiSite->setId($site->getId())
            ->setPath($site->getPath())
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
