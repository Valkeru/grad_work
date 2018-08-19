<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 2:27
 */

namespace App\Helpers;

class PasswordHelper
{
    public static function hashPassword(string $password)
    {
        return password_hash($password, PASSWORD_ARGON2I, ['threads' => 4]);
    }
}
