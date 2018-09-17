<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 14.08.18
 * Time: 19:09
 */

namespace App\Security;

use App\Entity\Customer;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use App\Helpers\BearerHelper;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use App\Exception\InvalidTokenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class CustomerTokenAuthenticator implements SimplePreAuthenticatorInterface
{
    /**
     * @var string
     */
    private $publicKeyFile;

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * CustomerTokenAuthenticator constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->publicKeyFile = 'file://' . $container->getParameter('app.public.public_key');
        $this->cache         = $container->get('app.cache.customer_token_blacklist');
    }

    /**
     * @param Request $request
     * @param         $providerKey
     *
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey): PreAuthenticatedToken
    {
        $tokenString = BearerHelper::extractTokenString($request);

        if (empty($tokenString)) {
            throw new AccessDeniedHttpException(NULL);
        }

        return new PreAuthenticatedToken('unknown_customer', $tokenString, $providerKey);
    }

    /**
     * @param TokenInterface        $token
     * @param UserProviderInterface $userProvider
     * @param                       $providerKey
     *
     * @return PreAuthenticatedToken
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
            throw new AccessDeniedHttpException(NULL);
        }

        try {
            /** @var Customer $user */
            $user = $userProvider->loadUserByUsername($jwt->getClaim('userName'));

            $iat = (new \DateTime())->setTimestamp($jwt->getClaim('iat'));
            if ($iat < $user->getAccountStatus()->getTokensInvalidationDate()
                || $iat < $user->getAccountStatus()->getPasswordChangeDate()
            ) {
                throw new AccessDeniedHttpException(NULL);
            }

            $now               = new \DateTime();
            $tokenUuid         = $jwt->getClaim('uuid');
            $blacklistedTokens = $this->cache->get((string)$user->getId(), []);

            foreach ($blacklistedTokens as $uuid => $expiration) {
                if ($expiration <= $now) {
                    unset($blacklistedTokens[$uuid]);
                    continue;
                }

                if ($uuid === $tokenUuid) {
                    throw new AccessDeniedHttpException(NULL);
                }
            }
        } catch (UsernameNotFoundException $e) {
            throw new AccessDeniedHttpException(NULL);
        }

        $user->setToken($jwt);

        return new PreAuthenticatedToken($user, $jwt, $providerKey, $user->getRoles());
    }

    /**
     * @param TokenInterface $token
     * @param                $providerKey
     *
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey): bool
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}
