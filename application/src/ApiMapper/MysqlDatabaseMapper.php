<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 02.10.18
 * Time: 11:51
 */

namespace App\ApiMapper;

use App\Entity\Database;
use App\Entity\DatabaseAccess;
use Valkeru\PublicApi\Structures\MysqlDatabase;

class MysqlDatabaseMapper
{
    public static function mapDatabase(Database $database): MysqlDatabase
    {
        $mysqlDatabase = new MysqlDatabase();

        $mysqlDatabase->setSuffix($database->getSuffix())
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
