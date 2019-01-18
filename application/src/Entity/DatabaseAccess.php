<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class DatabaseAccess
 *
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\DatabaseAccessRepository")
 * @ORM\Table(name="db_accesses", indexes={
 *          @ORM\Index(name="ix_access_db", columns={"db_id"})
 *      }, uniqueConstraints={
 *          @ORM\UniqueConstraint(name="ux_db_host", columns={"db_id", "host"})
 * }
 * )
 */
class DatabaseAccess
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Database
     *
     * @ORM\ManyToOne(targetEntity="Database", inversedBy="accesses")
     * @ORM\JoinColumn(name="db_id", referencedColumnName="id")
     */
    private $database;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, options={"default": "localhost"})
     */
    private $host = 'localhost';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }

    /**
     * @param Database $database
     *
     * @return DatabaseAccess
     */
    public function setDatabase(Database $database): DatabaseAccess
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return DatabaseAccess
     */
    public function setHost(string $host): DatabaseAccess
    {
        $this->host = $host;

        return $this;
    }
}
