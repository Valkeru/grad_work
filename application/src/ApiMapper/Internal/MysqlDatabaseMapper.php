<?php

namespace App\ApiMapper\Internal;

use App\Entity\Database;
use App\Entity\DatabaseAccess;
use Valkeru\PrivateApi\Structures\MysqlDatabase;

class MysqlDatabaseMapper
{
    public static function mapDatabase(Database $database): MysqlDatabase
    {
        $mysqlDatabase = new MysqlDatabase();

        $mysqlDatabase->setSuffix($database->getSuffix())
            ->setCustomer(CustomerMapper::mapCustomer($database->getCustomer()))
            ->setId($database->getId())
            ->setAllowedHosts(\call_user_func(function (Database $database) {
                $hosts = [];

                /** @var DatabaseAccess $access */
                foreach ($database->getAccesses() as $access) {
                    $hosts[] = $access->getHost();
                }

                return $hosts;
            }, $database));

        return $mysqlDatabase;
    }
}
