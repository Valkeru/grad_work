<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 27.05.18
 * Time: 20:10
 */

namespace App\EventSubscriber;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PublicApiRequestSubscriber implements EventSubscriberInterface
{
    private const PUBLIC_ROUTES = [
        '/login', '/loguot', '/register', '/security'
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
     * @return array
     */
    public static function getSubscribedEvents():array
    {
        return [
            KernelEvents::REQUEST => 'process'
        ];
    }

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

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function process(GetResponseEvent $event): void
    {
        if (!$this->shouldBeProcessed($event)) {
            return;
        }

        $this->verifyUser($event->getRequest());
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

    /**
     * @inheritdoc
     */
    private function isOpenRoute(Request $request): bool
    {
        if (!$this->isPublicPartEnabled()) {
            throw new UnauthorizedHttpException('');
        }

        $path = $request->getPathInfo();

        return \in_array($path, self::PUBLIC_ROUTES, true)
            || (bool)preg_match('#\/_wdt\/[a-z\d]+#', $path);
    }

    private function isPublicPartEnabled(): bool
    {
        return $this->container->getParameter('app.public_enabled');
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
