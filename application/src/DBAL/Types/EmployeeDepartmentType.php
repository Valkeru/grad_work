<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EmployeeDepartmentType extends AbstractEnumType
{
    protected $name = 'employee_department';

    public const DEPARTMENT_SUPPORT = 'support';
    public const DEPARTMENT_ADMIN   = 'admin';
    public const DEPARTMENT_DEV     = 'development';
    public const DEPARTMENT_MANAGER = 'manager';

    protected static $choices = [
        self::DEPARTMENT_SUPPORT => 'support',
        self::DEPARTMENT_ADMIN   => 'admin',
        self::DEPARTMENT_DEV     => 'development',
        self::DEPARTMENT_MANAGER => 'manager',
    ];
}
