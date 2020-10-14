<?php

namespace App\Models\Traits\Authenticated;

use Illuminate\Support\Str;

trait CreateToken
{
    public static function createToken()
    {
        return md5(microtime() . config('app.key')) . md5(Str::random());
    }
}