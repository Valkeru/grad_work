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
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\{
    NotFoundHttpException, AccessDeniedHttpException, UnauthorizedHttpException, MethodNotAllowedHttpException,
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
            KernelEvents::EXCEPTION => 'process'
        ];
    }

    public function process(GetResponseForExceptionEvent $event): void
    {
        $event->allowCustomResponseCode();
        $exception = $event->getException();

        try {
            throw $exception;
        } catch (UnauthorizedHttpException $unauthorizedHttpException) {

            $event->setResponse(
                new Response($unauthorizedHttpException->getMessage(), Response::HTTP_UNAUTHORIZED)
            );

        } catch (AccessDeniedHttpException $accessDeniedHttpException) {

            $event->setResponse(new Response(NULL, Response::HTTP_FORBIDDEN));

        } catch (NotFoundHttpException $notFoundHttpException) {

            $event->setResponse(new Response(NULL, Response::HTTP_NOT_FOUND));

        } catch (MethodNotAllowedHttpException $methodNotAllowedHttpException) {

            $event->setResponse(new Response(NULL, Response::HTTP_METHOD_NOT_ALLOWED));

        } catch (ProtobufException $protobufException) {

            $event->setResponse(
                new JsonResponse(['error' => $protobufException->getMessage()], Response::HTTP_BAD_REQUEST)
            );

        } catch (ValidatorException $validatorException) {

            $event->setResponse(
                JsonResponse::fromJsonString($validatorException->getMessage(), Response::HTTP_BAD_REQUEST)
            );

        } catch (InvalidTokenException $invalidTokenException) {

            $event->setResponse(new JsonResponse(
                [
                    'error' => $invalidTokenException->getMessage()
                ], Response::HTTP_BAD_REQUEST
            ));

        } catch (\Throwable $throwable) {

            if (!$this->isInDebugMode) {
                $event->setResponse(new Response());
            } else {
                $event->setResponse(
                    new JsonResponse([
                        'error' => $throwable->getMessage()
                    ])
                );
            }

            $event->getResponse()->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (method_exists($exception, 'getHeaders')) {
            $event->getResponse()->headers->add($exception->getHeaders());
        }

        $this->logger->error($exception);
    }
}
