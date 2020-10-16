<?php

namespace App\Models\Traits\Order;

use Illuminate\Support\Facades\Cache;

trait CreateTakingCode
{
    //  创建取餐码
    public static function createTakingCode()
    {
        $today = date('d');

        $cacheKey = 'taking_code-' . store()->admin_user_id .'-' . $today;

        if (Cache::has($cacheKey)) {
            $takingCode = $today . str_pad((substr(Cache::get($cacheKey), -3) + 1), 3, 0, STR_PAD_LEFT);
        } else {
            $takingCode = $today . '001';
        }

        Cache::put($cacheKey, $takingCode, 86400);

        return $takingCode;
    }
}