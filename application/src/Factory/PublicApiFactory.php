<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 17.08.18
 * Time: 0:04
 */

namespace App\Factory;

use App\Exception\ProtobufException;
use Google\Protobuf\Internal\Message;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PublicApiFactory
{
    /**
     * @param RequestStack $stack
     * @param string       $class Message class name
     *
     * @return Message
     * @throws ProtobufException
     */
    public static function makePublicApiRequest(RequestStack $stack, string $class): Message
    {
        /** @var Message $request */
        $request = new $class();
        try {
            $request->mergeFromJsonString(
                self::extractRequestContent(
                    $stack->getCurrentRequest()
                )
            );
        } catch (\Throwable $e) {
            throw new ProtobufException($e->getMessage());
        }

        return $request;
    }

    /**
     * @param null|Request $request
     *
     * @return string
     * @throws BadRequestHttpException
     */
    private static function extractRequestContent(?Request $request): string
    {
        if ($request === NULL) {
            throw new BadRequestHttpException('Request extraction failed');
        }

        return $request->getContent();
    }
}
