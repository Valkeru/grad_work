<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 15:24
 */

namespace App\Controller\PublicApi\v1;

use App\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FtpController
 *
 * @package App\Controller\PublicApi\v1
 *
 * @Route("/ftp")
 * @Security("has_role('ROLE_CUSTOMER')")
 * @method Customer getUser()
 */
class FtpController extends Controller
{
    /**
     * @Route(methods={"GET"})
     * @return JsonResponse
     */
    public function actionList(): JsonResponse
    {
        return new JsonResponse();
    }
}
