<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 05.06.18
 * Time: 21:48
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PrivateApiRequestSubscriber extends BaseSubscriber implements EventSubscriberInterface
{
    private const PUBLIC_ROUTES = [
        '/login', '/logout'
    ];

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'process'
        ];
    }

    public function process(GetResponseEvent $event)
    {
        if (!$this->shouldBeProcessed($event)) {
            return;
        }
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
