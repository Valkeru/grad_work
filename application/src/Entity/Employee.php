<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 06.06.18
 * Time: 21:18
 */

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Worker
 *
 * @package App\Entity
 * @method \App\Repository\EmployeeRepository find(\Doctrine\ORM\EntityManager $entityManager)
 *
 * @ORM\Table(name="workers")
 * @ORM\Entity(repositoryClass="App\Repository\EmployeeRepository")
 */
class Employee extends BaseEntity
{
    public const STATUS_PROBATION = 'probation';
    public const STATUS_WORKING   = 'working';
    public const STATUS_FIRED     = 'fired';

    public const POSITION_SUPPORT = 'support';
    public const POSITION_ADMIN   = 'admin';
    public const POSITION_CODER   = 'coder';
    public const POSITION_MANAGER = 'manager';
    public const POSITION_DEVOPS  = 'devops';

    public const DEPARTMENT_SUPPORT = 'support';
    public const DEPARTMENT_ADMIN   = 'admin';
    public const DEPARTMENT_DEV     = 'development';
    public const DEPARTMENT_MANAGER = 'manager';
    public const DEPARTMENT_BILL    = 'bill';

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
    private $name;

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
    private $status = self::STATUS_PROBATION;

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
     * @return Employee
     */
    public function setLogin(string $login): self
    {
        if ($this->login === NULL) {
            $this->login = $login;
        }

        return $this;
    }

    /**
     * @param string $password
     *
     * @return Employee
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
     * @return Employee
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
     * @return Employee
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
     * @return Employee
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
     * @return Employee
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
     * @return Employee
     */
    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
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
     * @return Employee
     */
    public function setName(string $name): Employee
    {
        $this->name = $name;

        return $this;
    }
}
