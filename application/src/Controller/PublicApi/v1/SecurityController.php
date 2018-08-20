<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.06.18
 * Time: 17:10
 */

namespace App\Controller\PublicApi\v1;

use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PublicApi\Security\BlacklistTokenRequest;
use Valkeru\PublicApi\Security\ChangePasswordRequest;
use Valkeru\PublicApi\Security\InvalidateAllTokensRequest;
use Valkeru\PublicApi\Security\TokenInfoRequest;
use Valkeru\PublicApi\Security\TokenInfoResponse;

/**
 * Class SecurityController
 *
 * @package App\Controller\PublicApi\v1
 *
 * @Security("request.server.get('PUBLIC_ENABLED') === '1'", message="Under maintenance. Please try later")
 * @Security("has_role('ROLE_CUSTOMER_BLOCKED')")
 *
 * @Route("/security")
 */
class SecurityController extends Controller
{
    /**
     * @Route(methods={"GET"})
     */
    public function getPublicKey(): JsonResponse
    {
        $key = new Key('file://' . $this->getParameter('app.public.public_key'));

        return new JsonResponse([
            'key' => $key->getContent()
        ]);
    }

    /**
     * @Route("/token-info", methods={"GET"})
     *
     * @param TokenInfoRequest $request
     *
     * @return JsonResponse
     */
    public function actionTokenInfo(TokenInfoRequest $request): JsonResponse
    {
        $response = new TokenInfoResponse();
        /** @var Token $token */
        $token = $this->getUser()->getToken();
        $iat   = (new \DateTime())->setTimestamp($token->getClaim('iat'))->format('Y:m:d H:i:s');
        $exp   = (new \DateTime())->setTimestamp($token->getClaim('exp'))->format('Y:m:d H:i:s');

        $response->setIat($iat)->setExp($exp)->setUuid($token->getClaim('uuid'));

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/blacklist-token", methods={"GET"})
     *
     * @param BlacklistTokenRequest $request
     *
     * @return JsonResponse
     */
    public function actionBlacklistToken(BlacklistTokenRequest $request): JsonResponse
    {
        if (($uuid = $request->getUuid()) === '') {
            $uuid = $this->getUser()->getToken()->getClaim('uuid');
        }



        return JsonResponse::fromJsonString('{}');
    }

    /**
     * @Route("/blacklist-token/all", methods={"POST"})
     *
     * @param InvalidateAllTokensRequest $request
     *
     * @return JsonResponse
     */
    public function actionInvalidateAllokens(InvalidateAllTokensRequest $request): JsonResponse
    {
        return JsonResponse::fromJsonString('{}');
    }

    /**
     * @Route("/password", methods={"POST"})
     *
     * @param ChangePasswordRequest $request
     *
     * @return JsonResponse
     */
    public function actionChangePassword(ChangePasswordRequest $request): JsonResponse
    {
        return JsonResponse::fromJsonString('{}');
    }
}
