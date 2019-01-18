<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class EmployeePositionType extends AbstractEnumType
{
    protected $name = 'employee_position';

    public const POSITION_SUPPORT   = 'support';
    public const POSITION_ADMIN     = 'admin';
    public const POSITION_DEVELOPER = 'developer';
    public const POSITION_MANAGER   = 'manager';
    public const POSITION_DEVOPS    = 'devops';

    protected static $choices = [
        self::POSITION_SUPPORT   => 'support',
        self::POSITION_ADMIN     => 'admin',
        self::POSITION_DEVELOPER => 'developer',
        self::POSITION_MANAGER   => 'manager',
        self::POSITION_DEVOPS    => 'devops',
    ];
}
