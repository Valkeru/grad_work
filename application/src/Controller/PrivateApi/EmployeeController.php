<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 12.06.18
 * Time: 23:20
 */

namespace App\Controller\PrivateApi;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("", methods={"PUT"})
     *
     * @return Response
     */
    public function newEmployeeAction(): Response
    {
        return new Response();
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"PATCH"})
     *
     * @return Response
     */
    public function editEmployeeAction(): Response
    {
        return new Response();
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"POST"})
     *
     * @return Response
     */
    public function blockEmployeeAction(): Response
    {
        return new Response();
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET"})
     *
     * @return Response
     */
    public function employeeInfoAction(): Response
    {
        return new Response();
    }
}
