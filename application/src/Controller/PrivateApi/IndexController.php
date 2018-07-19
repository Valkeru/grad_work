<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 05.06.18
 * Time: 20:42
 */

namespace App\Controller\PrivateApi;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
