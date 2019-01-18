<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Request;

class BearerHelper
{
    public static function extractTokenString(Request $request): string
    {
        return str_replace('Bearer ', '', $request->headers->get('Authorization', ''));
    }
}
