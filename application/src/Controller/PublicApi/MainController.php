<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 27.05.18
 * Time: 15:23
 */

namespace App\Controller\PublicApi;

use App\Service\AuthorizationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Valkeru\GradWork\api\DummyMessage;

class MainController extends Controller
{
    public function __construct()
    {
        usleep(1);
    }
    /**
     * @return Response
     * @Route("/")
     */
    public function actionRegister()
    {
        return new Response();
    }
}
