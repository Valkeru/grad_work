<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 27.05.18
 * Time: 20:10
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PublicApiRequestSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    private const PUBLIC_ROUTES = [
        '/login', '/loguot', '/register', '/security'
    ];

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'process'
        ];
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

    /**
     * @inheritdoc
     */
    protected function isOpenRoute(Request $request): bool
    {
        $path = $request->getPathInfo();

        return \in_array($path, self::PUBLIC_ROUTES, true)
            || (bool)preg_match('#\/_wdt\/[a-z\d]+#', $path);
    }
}
