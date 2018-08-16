<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.06.18
 * Time: 17:10
 */

namespace App\Controller\PublicApi;

use Lcobucci\JWT\Signer\Key;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SecurityController
 *
 * @package App\Controller\PublicApi
 *
 */
class SecurityController extends Controller
{
    /**
     * @Route("/security", methods={"GET"})
     */
    public function getPublicKey()
    {
        $key = new Key('file://' . $this->getParameter('app.public.public_key'));

        return new JsonResponse([
            'key' => $key->getContent()
        ]);
    }
}
