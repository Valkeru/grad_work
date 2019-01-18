<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class ServerTypeType extends AbstractEnumType
{
    protected $name = 'server_type';

    public const TYPE_HOSTING   = 'hosting';
    public const TYPE_SYSTEM    = 'system';
    public const TYPE_DEDICATED = 'dedicated';

    protected static $choices = [
        self::TYPE_HOSTING   => 'hosting',
        self::TYPE_SYSTEM    => 'system',
        self::TYPE_DEDICATED => 'dedicated',
    ];
}
