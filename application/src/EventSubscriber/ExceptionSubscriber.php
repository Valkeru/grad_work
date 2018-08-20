<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 12.06.18
 * Time: 19:26
 */

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Monolog\Logger;
use App\Exception\ProtobufException;
use App\Exception\InvalidTokenException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\{
    HttpException, NotFoundHttpException, AccessDeniedHttpException,
    UnauthorizedHttpException, MethodNotAllowedHttpException
};

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $isInDebugMode;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->isInDebugMode = $container->get('kernel')->isDebug();
        $this->logger        = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException'
        ];
    }

    public function onException(GetResponseForExceptionEvent $event): void
    {
        $event->allowCustomResponseCode();
        $exception = $event->getException();

        switch (\get_class($exception)) {
            case UnauthorizedHttpException::class:
                $event->setResponse(
                    new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED)
                );
                break;
            case NotFoundHttpException::class:
            case AccessDeniedHttpException::class:
            case MethodNotAllowedHttpException::class:
                /** @var HttpException $exception */
                $event->setResponse(new Response(NULL, $exception->getStatusCode()));
                break;
            case ProtobufException::class:
                $event->setResponse(
                    new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST)
                );
                break;
            case ValidatorException::class:
                $event->setResponse(
                    JsonResponse::fromJsonString($exception->getMessage(), Response::HTTP_BAD_REQUEST)
                );
                break;
            case InvalidTokenException::class:
                $event->setResponse(new JsonResponse(
                                        [
                                            'error' => $exception->getMessage()
                                        ], Response::HTTP_BAD_REQUEST
                                    ));
                break;
            case InsufficientAuthenticationException::class:
                $previous = $exception->getPrevious();
                $event->setResponse(new JsonResponse([
                    'message' => $previous->getMessage()
                ], Response::HTTP_FORBIDDEN));
                break;
            default:
                if (!$this->isInDebugMode) {
                    $event->setResponse(new Response());
                } else {
                    $event->setResponse(
                        new JsonResponse([
                                             'error' => $exception->getMessage()
                                         ])
                    );
                }

                $event->getResponse()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
                break;
        }

        if (method_exists($exception, 'getHeaders')) {
            $event->getResponse()->headers->add($exception->getHeaders());
        }

        $this->logger->error($exception);
    }
}
