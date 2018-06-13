<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 05.06.18
 * Time: 22:01
 */

namespace App\EventSubscriber;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class BaseSubscriber
{
    /**
     * @var string
     */
    protected $publicKeyFile;

    /**
     * @var string
     */
    protected $host;

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
     * Проверяет, что роут является публичным либо запросом к отладочным компнентам
     *
     * @param Request $request
     *
     * @return bool
     */
    abstract protected function isOpenRoute(Request $request) : bool;

    protected function shouldBeProcessed(GetResponseEvent $event): bool
    {
        if ($event->getRequest()->getHost() !== $this->host) {
            return false;
        }

        if (!$event->isMasterRequest()) {
            return false;
        }

        if ($this->isOpenRoute($event->getRequest())) {
            return false;
        }

        return true;
    }

    protected function verifyUser(Request $request): void
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
