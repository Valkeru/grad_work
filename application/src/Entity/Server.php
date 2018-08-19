<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 13:55
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Server
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ServerRepository")
 * @ORM\Table(name="servers", uniqueConstraints={
 *           @ORM\UniqueConstraint(name="ux_server_name", columns={"name"})
 *     })
 */
class Server
{
    public const TYPE_HOSTING                    = 'hosting';
    public const TYPE_SYSTEM                     = 'system';
    public const TYPE_DEDICATED                  = 'dedicated';
    public const TYPE_DEDICATED_WITH_MAINTENANCE = 'dedicated_with_maintanance';
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
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=15, nullable=false)
     * @Assert\Ip()
     */
    private $internalIp;

    /**
     * @var string
     * @ORM\Column(type="string", length=15, nullable=false)
     * @Assert\Ip()
     */
    private $mainIp;

    /**
     * @var string
     * @ORM\Column(type="string", length=15, nullable=false)
     * @Assert\Ip()
     */
    private $sslIp;

    /**
     * @var string
     * @ORM\Column(type="string", length=15, nullable=false)
     * @Assert\Ip()
     */
    private $outgoingIp;

    /**
     * @var string
     * @ORM\Column(type="string", columnDefinition="ENUM('hosting', 'system', 'dedicated', 'dedicated_with_maintanance')")
     */
    private $type = self::TYPE_HOSTING;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $registrationEnabled = false;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Customer", mappedBy="server")
     */
    private $customers;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Server
     */
    public function setName(string $name): Server
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getInternalIp(): string
    {
        return $this->internalIp;
    }

    /**
     * @param string $internalIp
     *
     * @return Server
     */
    public function setInternalIp(string $internalIp): Server
    {
        $this->internalIp = $internalIp;

        return $this;
    }

    /**
     * @return string
     */
    public function getMainIp(): string
    {
        return $this->mainIp;
    }

    /**
     * @param string $mainIp
     *
     * @return Server
     */
    public function setMainIp(string $mainIp): Server
    {
        $this->mainIp = $mainIp;

        return $this;
    }

    /**
     * @return string
     */
    public function getSslIp(): string
    {
        return $this->sslIp;
    }

    /**
     * @param string $sslIp
     *
     * @return Server
     */
    public function setSslIp(string $sslIp): Server
    {
        $this->sslIp = $sslIp;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutgoingIp(): string
    {
        return $this->outgoingIp;
    }

    /**
     * @param string $outgoingIp
     *
     * @return Server
     */
    public function setOutgoingIp(string $outgoingIp): Server
    {
        $this->outgoingIp = $outgoingIp;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    /**
     * @param Collection $customers
     *
     * @return Server
     */
    public function setCustomers(Collection $customers): Server
    {
        $this->customers = $customers;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Server
     */
    public function setType(string $type): Server
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRegistrationEnabled(): bool
    {
        return $this->registrationEnabled;
    }

    /**
     * @param bool $registrationEnabled
     *
     * @return Server
     */
    public function setRegistrationEnabled(bool $registrationEnabled): Server
    {
        $this->registrationEnabled = $registrationEnabled;

        return $this;
    }
}
