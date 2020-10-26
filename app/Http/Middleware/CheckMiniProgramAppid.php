<?php

namespace App\Http\Middleware;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Models\Store;
use Closure;
use Illuminate\Http\Request;

class CheckMiniProgramAppid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $appid = $request->header('appid');

        $store = Store::with('AdminUser')->where('mini_program_appid', $appid)->first();
        if (!$store) {
            throw new CustomException('商铺 认证失败', CustomCode::StoreCheckError);
        }

        $request->store = $store;

        return $next($request);
    }
}
