<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.06.18
 * Time: 17:10
 */

namespace App\Controller\PublicApi\v1;

use App\Entity\Customer;
use App\Service\SecurityService;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Valkeru\PublicApi\Security\{
    BlacklistTokenRequest, ChangePasswordRequest, ChangePasswordResponse, ChangePasswordResponse_Error,
    ChangePasswordResponse_Error_Code, ChangePasswordResponse_Success, InvalidateAllTokensRequest,
    InvalidateAllTokensResponse, InvalidateAllTokensResponse_Success, PublicKeyRequest, PublicKeyResponse,
    PublicKeyResponse_Success, TokenInfoRequest, TokenInfoResponse,
};

/**
 * Class SecurityController
 *
 * @method Customer getUser();
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
     * @var SecurityService
     */
    private $securityService;

    /**
     * SecurityController constructor.
     *
     * @param SecurityService $securityService
     */
    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * @Route("/public-key", methods={"GET"})
     *
     * @param PublicKeyRequest $request
     * @return JsonResponse
     */
    public function getPublicKey(PublicKeyRequest $request): JsonResponse
    {
        $response = new PublicKeyResponse();
        $key = new Key('file://' . $this->getParameter('app.public.public_key'));
        $response->setSuccess((new PublicKeyResponse_Success())->setKey($key->getContent()));

        return JsonResponse::fromJsonString($response->serializeToJsonString());
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
     * @Route("/blacklist-token", methods={"POST"})
     *
     * @param BlacklistTokenRequest $request
     *
     * @return JsonResponse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function actionBlacklistToken(BlacklistTokenRequest $request): Response
    {
        if (($uuid = $request->getUuid()) === '') {
            $uuid = $this->getUser()->getToken()->getClaim('uuid');
        }

        $this->securityService->blacklistCustomerToken($this->getUser(), $uuid);

        return new Response();
    }

    /**
     * @Route("/invalidate-all-tokens", methods={"POST"})
     *
     * @param InvalidateAllTokensRequest $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function actionInvalidateAllokens(InvalidateAllTokensRequest $request): JsonResponse
    {
        $response = new InvalidateAllTokensResponse();
        $token = $this->securityService->invalidateAllCustomerTokens($this->getUser());
        $response->setSuccess(
            (new InvalidateAllTokensResponse_Success())->setToken((string)$token)
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }

    /**
     * @Route("/password", methods={"POST"})
     *
     * @param ChangePasswordRequest $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function actionChangePassword(ChangePasswordRequest $request): JsonResponse
    {
        $response = new ChangePasswordResponse();
        $result = $this->securityService->changeCustomerPassword($this->getUser(), $request);

        if ($result !== true) {
            $response->setError(
                new ChangePasswordResponse_Error()
            );

            switch ($result['code']) {
                case SecurityService::PASSWORD_AND_CONFIRMATION_NOT_MATCHED:
                    $response->getError()->setCode(ChangePasswordResponse_Error_Code::NEW_PASSWORDS_IS_NOT_SAME);
            }

            $response->getError()->setMessage($result['message']);

            return JsonResponse::fromJsonString($response->serializeToJsonString());
        }

        $token = $this->securityService->authenticateCustomer($this->getUser());
        $response->setSuccess(
            (new ChangePasswordResponse_Success())->setToken((string)$token)
        );

        return JsonResponse::fromJsonString($response->serializeToJsonString());
    }
}
