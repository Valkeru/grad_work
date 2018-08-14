<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 14.08.18
 * Time: 18:55
 */

namespace App\Security;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class EmployeeTokenAuthenticator implements SimplePreAuthenticatorInterface
{
    /**
     * @var string
     */
    private $publicKeyFile;

    private $host;

    public function __construct(ContainerInterface $container)
    {
        $this->publicKeyFile = 'file://' . $container->getParameter('app.private.public_key');
        $this->host          = $container->getParameter('app.private_host');
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
        $tokenString = str_replace('Bearer ', '', $request->headers->get('Authorization', ''));

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

        $employee = $userProvider->loadUserByUsername($jwt->getClaim('employeeLogin'));

        return new PreAuthenticatedToken($employee, $tokenString, $providerKey);
    }

    public function supportsToken(TokenInterface $token, $providerKey): bool
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }
}
