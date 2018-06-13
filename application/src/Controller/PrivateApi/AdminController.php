<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 12.06.18
 * Time: 23:20
 */

namespace App\Controller\PrivateApi;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminController
 *
 * @package App\Controller\PrivateApi
 */
class AdminController extends Controller
{
    /**
     * @Route("/create-super-admin", methods={"PUT"})
     *
     * @param Request $request
     * @return Response
     */
    public function createSuperAdminAction(Request $request): Response
    {
        return new Response();
    }
}
