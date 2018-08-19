<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 15:13
 */

namespace App\Controller\PublicApi\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SiteController
 *
 * @package App\Controller\PublicApi\v1
 * @Route("/site")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class SiteController extends Controller
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
