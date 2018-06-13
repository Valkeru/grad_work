<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 12.06.18
 * Time: 23:20
 */

namespace App\Controller\PrivateApi;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EmployeeController
 *
 * @package App\Controller\PrivateApi
 *
 * @Route("/employee")
 */
class EmployeeController extends Controller
{
    /**
     * @Route("")
     * @Method("PUT")
     *
     * @return Response
     */
    public function newEmployeeAction(): Response
    {
        return new Response();
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"})
     * @Method("PATCH")
     *
     * @return Response
     */
    public function editEmployeeAction(): Response
    {
        return new Response();
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"})
     * @Method("POST")
     *
     * @return Response
     */
    public function blockEmployeeAction(): Response
    {
        return new Response();
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @return Response
     */
    public function employeeInfoAction(): Response
    {
        return new Response();
    }
}
