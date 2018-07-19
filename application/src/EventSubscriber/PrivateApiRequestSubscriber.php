<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 05.06.18
 * Time: 21:48
 */

namespace App\EventSubscriber;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PrivateApiRequestSubscriber implements EventSubscriberInterface
{
    private const PUBLIC_ROUTES = [
        '/login', '/logout'
    ];

    /**
     * @var string
     */
    private $publicKeyFile;

    /**
     * @var string
     */
    private $host;

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @param string $publicKeyFile
     */
    public function setPublicKeyFile(string $publicKeyFile): void
    {
        if ($this->publicKeyFile !== NULL) {
            return;
        }

        $this->publicKeyFile = $publicKeyFile;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'process'
        ];
    }

    public function process(GetResponseEvent $event): void
    {
        if (!$this->shouldBeProcessed($event)) {
            return;
        }
    }

    /**
     * @inheritdoc
     */
    private function isOpenRoute(Request $request): bool
    {
        $path = $request->getPathInfo();

        return \in_array($path, self::PUBLIC_ROUTES, true)
            || (bool)preg_match('#\/_wdt\/[a-z\d]+#', $path);
    }

    private function shouldBeProcessed(GetResponseEvent $event): bool
    {
        if (!$event->isMasterRequest()) {
            return false;
        }

        if ($event->getRequest()->getHost() !== $this->host) {
            return false;
        }

        if ($this->isOpenRoute($event->getRequest())) {
            return false;
        }

        return true;
    }

    private function verifyUser(Request $request): void
    {
        if (!$token = $request->headers->get('Authorization')) {
            throw new UnauthorizedHttpException('');
        }

        $signer    = new Sha256();
        $publicKey = new Key($this->publicKeyFile);
        $token     = (new Parser())->parse(str_replace('Bearer ', '', $token));
        if (!$token->verify($signer, $publicKey) || $token->isExpired()) {
            throw new UnauthorizedHttpException('');
        }
    }
}
