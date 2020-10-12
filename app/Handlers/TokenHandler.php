<?php

namespace App\Handlers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TokenHandler
{
    protected static $authenticateWays = [
        'mini_program',
    ];

    //  创建token
    //  ['mini_program-1' => 'token']
    //  ['token' => 'mini_program-1']
    public static function create($authenticatedId, $authenticateWay)
    {
        if (!in_array($authenticateWay, self::$authenticateWays)) {
            return '';
        }

        $tokenIndex = $authenticateWay . '-' . $authenticatedId;

        //  保证 token 是唯一的
        if ($oldToken = Cache::get($tokenIndex)) {
            Cache::forget($tokenIndex);
            Cache::forget($oldToken);
        }

        $ttl = config('custom-token.ttl');
        $token = md5(config('app.key') . $tokenIndex . microtime() . Str::random());
        Cache::put($tokenIndex, $token, $ttl);
        Cache::put($token, $tokenIndex, $ttl);

        return $token;
    }
}