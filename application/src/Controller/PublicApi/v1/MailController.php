<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 15:20
 */

namespace App\Controller\PublicApi\v1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MailController
 *
 * @package App\Controller\PublicApi\v1
 * @Route("/mail")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class MailController extends Controller
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
