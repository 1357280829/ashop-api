<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class AdminUrl implements CastsAttributes
{
    public function get($model, $key, $value, array $attributes)
    {
        return admin_url($value);
    }

    public function set($model, $key, $value, array $attributes)
    {
        return $value;
    }
}