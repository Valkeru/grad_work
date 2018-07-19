<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 12.06.18
 * Time: 19:26
 */

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'process'
        ];
    }

    public function process(GetResponseForExceptionEvent $event): void
    {
        $event->allowCustomResponseCode();

        try {
            throw $event->getException();
        } catch (UnauthorizedHttpException $e) {
            $event->setResponse(
                new Response($e->getMessage(), Response::HTTP_FORBIDDEN)
            );
        } catch (\Throwable $e) {
            //Допилить
        } finally {
            usleep(0);
        }
    }
}
