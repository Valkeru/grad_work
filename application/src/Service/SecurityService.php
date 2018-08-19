<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.06.18
 * Time: 13:58
 */

namespace App\Service;

use Ramsey\Uuid\Uuid;
use App\Entity\Customer;
use App\Entity\Employee;
use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Lcobucci\JWT\{
    Parser, Token, Builder, Signer\Key, Signer\Rsa\Sha256
};

/**
 * Class SecurityService
 *
 * @package App\Service
 */
class SecurityService
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var RedisCache
     */
    private $customerTokenBlacklistCache;

    /**
     * @var RedisCache
     */
    private $emoloyeeTokenBlacklistCache;

    /**
     * AuthenticationService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container                   = $container;
        $this->customerTokenBlacklistCache = $container->get('app.cache.customer_token_blacklist');
        $this->emoloyeeTokenBlacklistCache = $container->get('app.cache.employee_token_blacklist');
    }

    /**
     * @param Customer      $customer
     * @param Employee|NULL $employee
     *
     * @return Token
     * @throws \Exception
     */
    public function authenticateCustomer(Customer $customer, Employee $employee = NULL): Token
    {
        $privateKey = new Key('file://' . $this->container->getParameter('app.public.private_key'));
        $now        = new \DateTime();
        $builder    = (new Builder())
            ->setIssuedAt($now->getTimestamp())
            ->set('userId', $customer->getId())
            ->set('userName', $customer->getLogin())
            ->set('uuid', (string)Uuid::uuid4());

        if ($employee !== NULL) {
            $builder->set('employeeId', $employee->getId())
                ->setExpiration($now->add(new \DateInterval('PT1H'))->getTimestamp());
        } else {
            $builder->setExpiration($now->add(new \DateInterval('P1W'))->getTimestamp());
        }

        return $builder->sign(new Sha256(), $privateKey)->getToken();
    }

    /**
     * @param Customer $customer
     * @param string   $tokenString
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function blacklistCustomerToken(Customer $customer, string $tokenString): void
    {
        $token = (new Parser())->parse($tokenString);
        $uuid  = $token->getClaim('uuid');

        /** @var string[] $blacklist */
        $blacklist        = $this->customerTokenBlacklistCache->get((string)$customer->getId(), []);
        $blacklist[$uuid] = (new \DateTime())->add(new \DateInterval('P1W'));
        $this->customerTokenBlacklistCache->set((string)$customer->getId(), $blacklist);
    }
}
