<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Domain;
use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class SiteService
 *
 * @package App\Service
 */
class SiteService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SiteService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Customer $customer
     * @param string   $path
     *
     * @return Site
     */
    public function createSite(Customer $customer, string $path): Site
    {
        $fullPath = sprintf('%s/%s', $customer->getHomeDir(), $path);
        $siteExists = $this->entityManager->getRepository(Site::class)
            ->findByCustomer($customer)
            ->findByPath($fullPath)->one() !== NULL;

        if ($siteExists) {
            throw new BadRequestHttpException(
                sprintf('Site %s already exists', $path)
            );
        }

        $site = new Site();
        $site->setPath($fullPath)->setCustomer($customer);

        $this->entityManager->persist($site);
        $this->entityManager->flush();

        $this->entityManager->refresh($site);

        return $site;
    }

    /**
     * @param Site $site
     */
    public function deleteSite(Site $site): void
    {
        $this->entityManager->remove($site);
        $this->entityManager->flush();
    }

    /**
     * @param Site   $site
     * @param Domain $domain
     *
     * @return Site|bool
     */
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

    /**
     * @param Site   $site
     * @param Domain $domain
     *
     * @return Site|bool
     */
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
