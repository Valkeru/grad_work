<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 16.08.18
 * Time: 9:53
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AccountStatus
 *
 * @package App\Entity
 * @ORM\Entity()
 * @ORM\Table()
 */
class AccountStatus
{
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Customer
     * @ORM\OneToOne(targetEntity="App\Entity\Customer", mappedBy="id")
     * @ORM\JoinColumn(name="cust_id", referencedColumnName="id")
     */
    private $customer;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $isBlocked = false;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false, options={"default":"CURRENT_TIMESTAMP"})
     */
    private $registrationDate;

    private $server;

    public function __construct()
    {
        $this->registrationDate = new \DateTime();
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
     * @return AccountStatus
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
     * @return AccountStatus
     */
    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param mixed $server
     * @return AccountStatus
     */
    public function setServer($server): self
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationDate(): \DateTime
    {
        return $this->registrationDate;
    }
}
