<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 19.08.18
 * Time: 3:11
 */

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Request;

class BearerHelper
{
    public static function extractTokenString(Request $request): string
    {
        return str_replace('Bearer ', '', $request->headers->get('Authorization', ''));
    }
}
