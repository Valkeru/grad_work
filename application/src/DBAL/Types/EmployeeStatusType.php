<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EmployeeStatusType extends AbstractEnumType
{
    protected $name = 'employee_status';

    public const STATUS_PROBATION = 'probation';
    public const STATUS_WORKING   = 'working';
    public const STATUS_FIRED     = 'fired';

    protected static $choices = [
        self::STATUS_PROBATION => 'probation',
        self::STATUS_WORKING   => 'working',
        self::STATUS_FIRED     => 'fired',
    ];
}
