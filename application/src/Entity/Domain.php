<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Domain
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\DomainRepository")
 * @ORM\Table(name="domains", uniqueConstraints={
 *          @ORM\UniqueConstraint(name="ux_domain_fqdn", columns={"fqdn"})
 *     },
 *     indexes={
 *          @ORM\Index(name="ix_domain_customer", columns={"cust_id"}),
 *          @ORM\Index(name="ix_domain_site", columns={"site_id"}),
 *     }
 * )
 */
class Domain
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $fqdn;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="domains", cascade={"persist"})
     * @ORM\JoinColumn(name="cust_id", referencedColumnName="id", nullable=false)
     */
    private $customer;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $isBlocked = false;

    /**
     * @var Site|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="domains")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=true)
     */
    private $site;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="\App\Entity\Mailbox", mappedBy="domain", cascade={"remove"})
     */
    private $mailboxes;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFqdn(): string
    {
        return $this->fqdn;
    }

    /**
     * @param string $fqdn
     *
     * @return Domain
     */
    public function setFqdn(string $fqdn): self
    {
        $this->fqdn = $fqdn;

        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     *
     * @return Domain
     */
    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    /**
     * @param bool $isBlocked
     *
     * @return Domain
     */
    public function setIsBlocked(bool $isBlocked): Domain
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    /**
     * @return Site
     */
    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @param Site $site
     *
     * @return Domain
     */
    public function setSite(?Site $site): Domain
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMailboxes(): Collection
    {
        return $this->mailboxes;
    }
}
