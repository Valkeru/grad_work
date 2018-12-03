<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 03.12.18
 * Time: 22:25
 */

namespace App\ApiMapper\Internal;

use App\Entity\Employee;
use Valkeru\PrivateApi\Structures\Employee as EmployeeMessage;

class EmployeeMapper
{
    public static function mapEmployee(Employee $employee): EmployeeMessage
    {
        $employeeMessage = new EmployeeMessage();

        $employeeMessage->setId($employee->getId())
            ->setLogin($employee->getLogin())
            ->setName($employee->getName())
            ->setEmail($employee->getEmail())
            ->setDepartment($employee->getDepartment())
            ->setPosition($employee->getPosition())
            ->setState($employee->getStatus());

        return $employeeMessage;
    }
}
