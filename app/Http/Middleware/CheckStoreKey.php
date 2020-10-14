<?php

namespace App\Http\Middleware;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Models\Store;
use Closure;
use Illuminate\Http\Request;

class CheckStoreKey
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
        $storeKey = $request->header('store-key');

        $store = Store::with('AdminUser')->where('key', $storeKey)->first();
        if (!$store) {
            throw new CustomException('商铺 认证失败', CustomCode::StoreCheckError);
        }

        $request->store = $store;

        return $next($request);
    }
}
