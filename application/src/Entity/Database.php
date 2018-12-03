<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.10.18
 * Time: 5:26
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Database
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\DatabaseRepository")
 * @ORM\Table(name="`databases`", indexes={
 *          @ORM\Index(name="ix_database_suffix", columns={"suffix"}),
 *          @ORM\Index(name="ix_cust_id", columns={"cust_id"})
 *      }, uniqueConstraints={
 *          @ORM\UniqueConstraint(name="ux_dadabase_suffix", columns={"suffix", "cust_id"})
 *      }
 * )
 */
class Database
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
    private $suffix;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\JoinColumn(name="cust_id", referencedColumnName="id", nullable=false)
     */
    private $customer;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="DatabaseAccess", mappedBy="database", cascade={"persist", "remove"})
     */
    private $accesses;

    public function __construct()
    {
        $this->accesses = new ArrayCollection();
        $defaultAccess = new DatabaseAccess();
        $this->accesses->add($defaultAccess);
        $defaultAccess->setDatabase($this);
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
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     *
     * @return Database
     */
    public function setSuffix(string $suffix): Database
    {
        $this->suffix = $suffix;

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
     * @return Database
     */
    public function setCustomer(Customer $customer): Database
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAccesses(): Collection
    {
        return $this->accesses;
    }

    /**
     * @param Collection $accesses
     *
     * @return Database
     */
    public function setAccesses(Collection $accesses): Database
    {
        $this->accesses = $accesses;

        return $this;
    }
}
