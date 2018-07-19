<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 27.05.18
 * Time: 23:27
 */

namespace App\Controller\PublicApi;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController
 *
 * @package App\Controller\PublicApi
 *
 * @route("/register")
 */
class RegistrationController extends Controller
{
    /**
     * @Route("")
     */
    public function actionIndex()
    {
        return new JsonResponse();
    }
}
