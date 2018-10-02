<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.10.18
 * Time: 0:59
 */

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Domain;
use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;

class SiteService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createSite(Customer $customer, string $path): Site
    {
        $site = new Site();
        $site->setPath(sprintf('/home/%s/%s', $customer->getLogin(), $path))
            ->setCustomer($customer);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        $this->entityManager->refresh($site);

        return $site;
    }

    public function attachDomain(Site $site, Domain $domain)
    {
        if ($site->getCustomer() !== $domain->getCustomer()) {
            return false;
        }

        $domain->setSite($site);

        $this->entityManager->persist($domain);
        $this->entityManager->flush();
        $this->entityManager->refresh($site);

        return $site;
    }

    public function detachDomain(Site $site, Domain $domain)
    {
        if ($site->getCustomer() !== $domain->getCustomer()) {
            return false;
        }

        $domain->setSite(NULL);

        $this->entityManager->persist($domain);
        $this->entityManager->flush();
        $this->entityManager->refresh($site);

        return $site;
    }
}
