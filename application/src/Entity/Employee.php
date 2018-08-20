<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 06.06.18
 * Time: 21:18
 */

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Helpers\PasswordHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Worker
 *
 * @package App\Entity
 * @method \App\Repository\EmployeeRepository find(\Doctrine\ORM\EntityManager $entityManager)
 *
 * @ORM\Table(name="workers", indexes={
 *     @ORM\Index(name="idx_name", columns={"name"})
 * }, uniqueConstraints={
 *              @ORM\UniqueConstraint(name="ux_login", columns={"login"}),
 *              @ORM\UniqueConstraint(name="ux_email", columns={"email"})
 *          }
 *     )
 * @ORM\Entity(repositoryClass="App\Repository\EmployeeRepository")
 */
class Employee extends BaseEntity
{
    public const STATUS_PROBATION = 'probation';
    public const STATUS_WORKING   = 'working';
    public const STATUS_FIRED     = 'fired';

    public const POSITION_SUPPORT   = 'support';
    public const POSITION_ADMIN     = 'admin';
    public const POSITION_DEVELOPER = 'developer';
    public const POSITION_MANAGER   = 'manager';
    public const POSITION_DEVOPS    = 'devops';

    public const DEPARTMENT_SUPPORT = 'support';
    public const DEPARTMENT_ADMIN   = 'admin';
    public const DEPARTMENT_DEV     = 'development';
    public const DEPARTMENT_MANAGER = 'manager';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=95, nullable=false)
     *
     * @Assert\Length(
     *     min=95,
     *     max=95,
     *     exactMessage="Password hash may consist of only {{ limit }} symbols"
     * )
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^\$argon2i\$v=19\$m=1024,t=2,p=4\$.{66}$/", message="Invalid password hash")
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('support', 'admin', 'development', 'manager', 'bill')", options={"default":"support"})
     */
    private $department;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('support', 'admin', 'developer', 'manager', 'devops')", options={"default":"support"})
     */
    private $position;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('probation', 'working', 'fired')")
     */
    private $status = self::STATUS_PROBATION;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $isAdmin = false;

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
        $this->password = PasswordHelper::hashPassword($password);

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

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        if ($this->isAdmin) {
            return ['ROLE_ADMIN'];
        }

        return ['ROLE_EMPLOYEE'];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->login;
    }
}
