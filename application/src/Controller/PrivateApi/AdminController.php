<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 12.06.18
 * Time: 23:20
 */

namespace App\Controller\PrivateApi;

use App\DBAL\Types\EmployeeDepartmentType;
use App\DBAL\Types\EmployeePositionType;
use App\Entity\Employee;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminController
 *
 * @package App\Controller\PrivateApi
 */
class AdminController extends Controller
{
    /**
     * @Route("/create-super-admin", methods={"PUT"})
     *
     * @param Request $request
     * @return Response
     */
    public function createSuperAdminAction(Request $request): Response
    {
        $admin = new Employee();
        $admin->setLogin('')->setPassword('')
            ->setStatus(Employee::STATUS_WORKING)
            ->setEmail('valkeru@valkeru.ru')
            ->setIsAdmin(true)
            ->setDepartment(EmployeeDepartmentType::DEPARTMENT_DEV)
            ->setPosition(EmployeePositionType::POSITION_DEVELOPER);

        $this->getDoctrine()->getManager()->persist($admin);
        $this->getDoctrine()->getManager()->flush();

        return new Response();
    }
}
