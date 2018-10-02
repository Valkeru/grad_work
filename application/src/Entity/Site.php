<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 17.09.18
 * Time: 12:16
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Site
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 * @ORM\Table(name="sites")
 */
class Site
{
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $path;

    /**
     * @var Customer
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="sites")
     * @ORM\JoinColumn(name="cust_id", referencedColumnName="id", nullable=false)
     */
    private $customer;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Domain", mappedBy="site")
     */
    private $domains;

    public function __construct()
    {
        $this->domains = new ArrayCollection();
    }

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
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Site
     */
    public function setPath(string $path): Site
    {
        $this->path = $path;

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
     * @return Site
     */
    public function setCustomer(Customer $customer): Site
    {
        if ($this->customer === NULL) {
            $this->customer = $customer;
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getDomains(): Collection
    {
        return $this->domains;
    }

    /**
     * @param Collection $domains
     *
     * @return Site
     */
    public function setDomains(Collection $domains): Site
    {
        $this->domains = $domains;

        return $this;
    }
}
