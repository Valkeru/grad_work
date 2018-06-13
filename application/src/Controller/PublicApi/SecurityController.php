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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class SecurityController
 *
 * @package App\Controller\PublicApi
 *
 */
class SecurityController extends Controller
{
    /**
     * @Route("/security")
     * @Method("GET")
     */
    public function getPublicKey()
    {
        $key = new Key('file://' . $this->get('kernel')->getProjectDir() . '/keys/public.key');

        return new JsonResponse([
            'key' => $key->getContent()
        ]);
    }
}
