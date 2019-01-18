<?php

namespace App\Service;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use App\Entity\Customer;
use App\Entity\Employee;
use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Lcobucci\JWT\{
    Parser, Token, Builder, Signer\Key, Signer\Rsa\Sha256
};
use Valkeru\PublicApi\Security\ChangePasswordRequest;

/**
 * Class SecurityService
 *
 * @package App\Service
 */
class SecurityService
{
    public const INVALID_PASSWORD = 1;

    public const PASSWORD_AND_CONFIRMATION_NOT_MATCHED = 2;

    public const INVALID_NEW_PASSWORD = 3;

    public const PASSWORD_REGEX = 'A-z\d\{\}\/|\\_\(\)&%\#\^\-\+\=';

    /**
     * @var RedisCache
     */
    private $customerTokenBlacklistCache;

    /**
     * @var RedisCache
     */
    private $employeeTokenBlacklistCache;

    /**
     * @var Key
     */
    private $publicApiKeyFile;

    /**
     * @var Key
     */
    private $privateApiKeyFile;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * AuthenticationService constructor.
     *
     * @param ContainerInterface $container
     * @param LoggerInterface    $logger
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->publicApiKeyFile            = new Key('file://' . $container->getParameter('app.public.private_key'));
        $this->privateApiKeyFile           = new Key('file://' . $container->getParameter('app.private.private_key'));
        $this->entityManager               = $container->get('doctrine.orm.entity_manager');
        $this->logger                      = $logger;
        $this->customerTokenBlacklistCache = $container->get('app.cache.customer_token_blacklist');
        $this->employeeTokenBlacklistCache = $container->get('app.cache.employee_token_blacklist');
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
        $now     = new \DateTime();
        $builder = (new Builder())
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

        return $builder->sign(new Sha256(), $this->publicApiKeyFile)->getToken();
    }

    /**
     * @param Employee $employee
     *
     * @return Token
     * @throws \Exception
     */
    public function authenticateEmployee(Employee $employee): Token
    {
        $now     = new \DateTime();
        $builder = (new Builder())
            ->setIssuedAt($now->getTimestamp())
            ->set('userId', $employee->getId())
            ->set('employeeLogin', $employee->getLogin())
            ->set('uuid', (string)Uuid::uuid4())
            ->setExpiration($now->add(new \DateInterval('P1W'))->getTimestamp());

        return $builder->sign(new Sha256(), $this->privateApiKeyFile)->getToken();
    }

    /**
     * @param Customer $customer
     * @param string   $tokenString Token or UUID
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function blacklistCustomerToken(Customer $customer, string $tokenString): void
    {
        if (!Uuid::isValid($tokenString)) {
            $token = (new Parser())->parse($tokenString);
            $uuid  = $token->getClaim('uuid');
        } else {
            $uuid = $tokenString;
        }

        /** @var string[] $blacklist */
        $blacklist        = $this->customerTokenBlacklistCache->get((string)$customer->getId(), []);
        $blacklist[$uuid] = (new \DateTime())->add(new \DateInterval('P1W'));
        $this->customerTokenBlacklistCache->set((string)$customer->getId(), $blacklist);
    }

    /**
     * @param Employee $employee
     * @param string   $tokenString
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function blacklistEmployeeToken(Employee $employee, string $tokenString): void
    {
        if (!Uuid::isValid($tokenString)) {
            $token = (new Parser())->parse($tokenString);
            $uuid  = $token->getClaim('uuid');
        } else {
            $uuid = $tokenString;
        }

        /** @var string[] $blacklist */
        $blacklist        = $this->employeeTokenBlacklistCache->get((string)$employee->getId(), []);
        $blacklist[$uuid] = (new \DateTime())->add(new \DateInterval('P1W'));
        $this->employeeTokenBlacklistCache->set((string)$employee->getId(), $blacklist);
    }

    /**
     * @param Customer $customer
     *
     * @return Token
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function invalidateAllCustomerTokens(Customer $customer): Token
    {
        $customer->getAccountStatus()->setTokensInvalidationDate(new \DateTime());
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return $this->authenticateCustomer($customer);
    }

    /**
     * @param Customer              $customer
     * @param ChangePasswordRequest $passwordRequest
     *
     * @return array|bool
     * @throws \Exception
     */
    public function changeCustomerPassword(Customer $customer, ChangePasswordRequest $passwordRequest)
    {
        $newPassword          = $passwordRequest->getNewPassword();
        $passwordConfirmation = $passwordRequest->getNewPasswordConfirmation();
        $oldPassword          = $passwordRequest->getOldPassword();

        if (!$customer->verifyPassword($oldPassword)) {
            return [
                'code'    => self::INVALID_PASSWORD,
                'message' => 'Invalid old password'
            ];
        }

        if (!self::validatePassword($newPassword)) {
            return [
                'code'    => self::INVALID_NEW_PASSWORD,
                'message' => 'New password should has length at least 8 symbols and consist of symbols A-z, 0-9, {, }, /, |, \, _, (, ), &, %, #, ^, -, +, ='
            ];
        }

        if ($newPassword !== $passwordConfirmation) {
            return [
                'code'    => self::PASSWORD_AND_CONFIRMATION_NOT_MATCHED,
                'message' => 'Password and confirmation don\'t match'
            ];
        }

        $this->entityManager->beginTransaction();
        try {
            $customer->setPassword($passwordRequest->getNewPassword())
                ->getAccountStatus()->setPasswordChangeDate(new \DateTime());

            $this->entityManager->persist($customer);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return true;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error($e);

            throw $e;
        }
    }

    public static function validatePassword(string $password): bool
    {
        return \preg_match(sprintf('#[%s]{8,}#', self::PASSWORD_REGEX), $password);
    }
}
