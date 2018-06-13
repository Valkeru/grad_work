<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 27.05.18
 * Time: 23:27
 */

namespace App\Controller\PublicApi;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @route("")
     */
    public function actionIndex()
    {
        return new JsonResponse();
    }
}
