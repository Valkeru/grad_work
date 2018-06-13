<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 27.05.18
 * Time: 23:28
 */

namespace App\Controller\PublicApi;

use App\Entity\Test;
use App\Kernel;
use App\Service\AuthorizationService;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthController
 *
 * @package App\Controller\PublicApi
 *
 * @route("/login")
 * @method("POST")
 */
class AuthController extends Controller
{
    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    /**
     * AuthController constructor.
     *
     * @param AuthorizationService $authorizationService
     */
    public function __construct(AuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
    }

    /**
     * @route("")
     */
    public function actionIndex(Request $request)
    {
        $privateKey = new Key('file://' . $this->getParameter('app.public.private_key'));
        $now = new \DateTime();
        $until = $now->add(new \DateInterval('P1W'));
        $token = (new Builder())
            ->setIssuedAt($now->getTimestamp())
            ->setExpiration($until->getTimestamp())
            ->set('userId', '')
            ->set('userName', '')
            ->sign(new Sha256(), $privateKey)
            ->getToken()
        ;

        usleep(1);

        return new JsonResponse([
            'token' => (string)$token
        ]);
    }
}
