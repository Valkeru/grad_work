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
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class CustomerTokenAuthenticator implements SimplePreAuthenticatorInterface
{
    /**
     * @var string
     */
    private $publicKeyFile;

    /**
     * CustomerTokenAuthenticator constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        if (!(bool)$container->getParameter('app.public_enabled')) {
            throw new UnauthorizedHttpException('');
        }

        $this->publicKeyFile = 'file://' . $container->getParameter('app.public.public_key');
    }

    /**
     * @param Request $request
     * @param         $providerKey
     *
     * @return PreAuthenticatedToken
     * @throws UnauthorizedHttpException
     */
    public function createToken(Request $request, $providerKey)
    {
        $tokenString = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));

        if (empty($tokenString)) {
            throw new UnauthorizedHttpException('');
        }

        return new PreAuthenticatedToken('unknown_customer', $tokenString, $providerKey);
    }

    /**
     * @param TokenInterface        $token
     * @param UserProviderInterface $userProvider
     * @param                       $providerKey
     *
     * @return PreAuthenticatedToken
     * @throws UnauthorizedHttpException
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey): PreAuthenticatedToken
    {
        /** @var string $tokenString */
        $tokenString = $token->getCredentials();
        $signer      = new Sha256();
        $publicKey   = new Key($this->publicKeyFile);
        $jwt         = (new Parser())->parse($tokenString);

        if (!$jwt->verify($signer, $publicKey) || $jwt->isExpired()) {
            throw new UnauthorizedHttpException('');
        }

        /** @var Customer $user */
        $user = $userProvider->loadUserByUsername($jwt->getClaim('userName'));
        $user->setCredentials($jwt);

        return new PreAuthenticatedToken($user, $tokenString, $providerKey);
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
