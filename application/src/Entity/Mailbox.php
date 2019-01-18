<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Mailbox
 *
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\MailboxRepository")
 * @ORM\Table(name="mailboxes", indexes={
 *     @ORM\Index(name="ix_mailbox_name", columns={"name"}),
 *     @ORM\Index(name="ix_mailbox_domain", columns={"domain_id"})
 * }, uniqueConstraints={
 *     @ORM\UniqueConstraint(name="ux_full_name", columns={"name", "domain_id"})
 * })
 */
class Mailbox
{
    /**
     * @var int
     * @ORM\Column(name="id", nullable=false, type="integer", options={"unsigned": true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(nullable=false, type="string")
     */
    private $name;

    /**
     * @var Domain
     * @ORM\ManyToOne(targetEntity="\App\Entity\Domain", inversedBy="mailboxes")
     * @ORM\JoinColumn(name="domain_id", referencedColumnName="id", nullable=false)
     */
    private $domain;

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
     * @return Mailbox
     */
    public function setName(string $name): Mailbox
    {
        if ($this->name === NULL) {
            $this->name = $name;
        }

        return $this;
    }

    /**
     * @return Domain
     */
    public function getDomain(): Domain
    {
        return $this->domain;
    }

    /**
     * @param Domain $domain
     *
     * @return Mailbox
     */
    public function setDomain(Domain $domain): Mailbox
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->name . '@' . $this->domain->getFqdn();
    }
}
