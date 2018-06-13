<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 05.06.18
 * Time: 20:42
 */

namespace App\Controller\PrivateApi;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * @Route("/")
     */
    public function actionIndex()
    {
        return new Response(1);
    }
}
