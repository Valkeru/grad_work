<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 14.08.18
 * Time: 18:55
 */

namespace App\Security;

use App\Entity\Employee;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use App\Helpers\BearerHelper;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use App\Exception\InvalidTokenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class EmployeeTokenAuthenticator implements SimplePreAuthenticatorInterface
{
    /**
     * @var string
     */
    private $publicKeyFile;

    /**
     * @var RedisCache
     */
    private $cache;

    public function __construct(ContainerInterface $container)
    {
        $this->publicKeyFile = 'file://' . $container->getParameter('app.private.public_key');
        $this->cache         = $container->get('app.cache.employee_token_blacklist');
    }

    /**
     * @param Request $request
     * @param         $providerKey
     *
     * @return PreAuthenticatedToken
     * @throws UnauthorizedHttpException
     */
    public function createToken(Request $request, $providerKey): PreAuthenticatedToken
    {
        $tokenString = BearerHelper::extractTokenString($request);

        if (empty($tokenString)) {
            throw new UnauthorizedHttpException('');
        }

        return new PreAuthenticatedToken('unknown_employee', $tokenString, $providerKey);
    }

    /**
     * @param TokenInterface|PreAuthenticatedToken $token
     * @param UserProviderInterface                $userProvider
     * @param                                      $providerKey
     *
     * @return PreAuthenticatedToken
     * @throws UnauthorizedHttpException
     * @throws InvalidTokenException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey): PreAuthenticatedToken
    {
        /** @var string $tokenString */
        $tokenString = $token->getCredentials();
        $signer      = new Sha256();
        $publicKey   = new Key($this->publicKeyFile);
        try {
            $jwt = (new Parser())->parse($tokenString);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidTokenException($e->getMessage());
        }

        if (!$jwt->verify($signer, $publicKey) || $jwt->isExpired()) {
            throw new UnauthorizedHttpException('');
        }

        try {
            /** @var Employee $employee */
            $employee          = $userProvider->loadUserByUsername($jwt->getClaim('employeeLogin'));
            $now               = new \DateTime();
            $tokenUuid         = $jwt->getClaim('uuid');
            $blacklistedTokens = $this->cache->get((string)$employee->getId(), []);

            foreach ($blacklistedTokens as $uuid => $expiration) {
                if ($expiration <= $now) {
                    unset($blacklistedTokens[$uuid]);
                    continue;
                }

                if ($uuid === $tokenUuid) {
                    throw new UnauthorizedHttpException('');
                }
            }
        } catch (UsernameNotFoundException $e) {
            throw new UnauthorizedHttpException('');
        }

        return new PreAuthenticatedToken($employee, $tokenString, $providerKey, $employee->getRoles());
    }

    public function supportsToken(TokenInterface $token, $providerKey): bool
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}
