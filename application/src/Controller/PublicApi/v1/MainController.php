<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 27.05.18
 * Time: 15:23
 */

namespace App\Controller\PublicApi\v1;

use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
    private $token;

    public function __construct(ContainerInterface $container)
    {
        $this->token = $container->get('security.token_storage');
    }

    /**
     * @return Response
     * @Route("/")
     */
    public function actionIndex()
    {
        /** @var Customer $u */
        $u = $this->getUser();
        return new Response();
    }
}
