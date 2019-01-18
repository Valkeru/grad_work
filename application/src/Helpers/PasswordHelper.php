<?php

namespace App\Helpers;

class PasswordHelper
{
    public static function hashPassword(string $password)
    {
        return password_hash($password, PASSWORD_ARGON2I, ['threads' => 4]);
    }
}
