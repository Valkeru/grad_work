<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 06.06.18
 * Time: 21:18
 */

namespace App\Entity;

class Worker
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $department;

    /**
     * @var string
     */
    private $position;

    /**
     * @var string
     */
    private $status = 'probation';

    /**
     * @var bool
     */
    private $isAdmin;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return Worker
     */
    public function setLogin(string $login): self
    {
        if ($this->login === NULL) {
            $this->login = $login;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return Worker
     */
    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Worker
     */
    public function setEmail(string $email): self
    {
        if ($this->email === NULL) {
            $this->email = $email;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDepartment(): string
    {
        return $this->department;
    }

    /**
     * @param string $department
     *
     * @return Worker
     */
    public function setDepartment(string $department): self
    {
        $this->department = $department;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @param string $position
     *
     * @return Worker
     */
    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Worker
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     *
     * @return Worker
     */
    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }
}
