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
use Lcobucci\JWT\Token;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class Customer
 *
 * @package App\Entity
 * @method \App\Repository\CustomerRepository find(\Doctrine\ORM\EntityManager $entityManager)
 *
 * @ORM\Table(name="customers")
 * @ORM\Entity(repositoryClass="App\Repository\CustomerRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Customer extends BaseEntity implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", nullable=false, unique=true)
     * @Assert\NotBlank()
     * @Assert\Regex("/[a-z0-9]+/")
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=200, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^\+\d+$/", message="Invalid phone")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=95, nullable=false)
     *
     * @Assert\Length(
     *     min=60,
     *     max=60,
     *     exactMessage="Password hash may consist of only {{ limit }} symbols"
     * )
     * @Assert\Regex(pattern="/^\$2y\$.{56}$/", message="Invalid password hash")
     */
    private $password;

    /**
     * JWT токен пользователя
     * @var Token
     */
    private $credentials;

    /**
     * @var AccountStatus
     * @ORM\OneToOne(targetEntity="App\Entity\AccountStatus", mappedBy="customer", cascade={"persist"})
     */
    private $accountStatus;

    /**
     * Customer constructor.
     */
    public function __construct()
    {
        //Связываем сущности между собой
        //Иначе при персисте нового пользователя придётся отдельно персистить статус
        $this->accountStatus = new AccountStatus();
        $this->accountStatus->setCustomer($this);
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
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return Customer
     */
    public function setLogin(string $login): self
    {
        $this->login = $login;

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
     * @return Customer
     */
    public function setName(string $name): self
    {
        $this->name = $name;

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
     * @return Customer
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return Customer
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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
     * @return Customer
     */
    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_ARGON2I, ['threads' => 4]);

        return $this;
    }

    /**
     * @return AccountStatus
     */
    public function getAccountStatus(): AccountStatus
    {
        return $this->accountStatus;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        if ($this->accountStatus->isBlocked()) {
            return ['ROLE_CUSTOMER_BLOCKED'];
        }

        return ['ROLE_CUSTOMER'];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getLogin();
    }

    /**
     * @return null|string
     */
    public function getSalt()
    {
        return NULL;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getLogin();
    }

    /**
     *
     */
    public function eraseCredentials(): void
    {
        $this->credentials = null;
    }
}
