<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 20.08.18
 * Time: 11:48
 */

namespace App\Controller\PrivateApi;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ServerController
 *
 * @package App\Controller\PrivateApi
 * @Route("/server")
 */
class ServerController
{
    public function actionList(): JsonResponse
    {
        return JsonResponse::fromJsonString('{}');
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return JsonResponse
     */
    public static function actionEditServerInfo(): JsonResponse
    {
        return JsonResponse::fromJsonString('{}');
    }
}
