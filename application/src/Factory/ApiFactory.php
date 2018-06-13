<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 10.06.18
 * Time: 9:41
 */

namespace App\Factory;

use Valkeru\GradWork\api\DummyMessage;

class ApiFactory
{
    public static function createApiMessage(...$id)
    {
        $z = DummyMessage::class;
        return new DummyMessage();
    }
}
