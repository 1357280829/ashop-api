<?php

namespace App\Http\Middleware;

use App\Enums\CustomCode;
use App\Exceptions\CustomException;
use App\Models\Authenticated;
use Closure;
use Illuminate\Http\Request;

class CheckAuthenticatedToken
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
        $token = $request->bearerToken();
        if (!$token) {
            throw new CustomException('token 不存在', CustomCode::AuthError);
        }

        $authenticated = Authenticated::where('token', $token)->first();
        if (!$authenticated) {
            throw new CustomException('token 非法或已失效', CustomCode::AuthError);
        }

        if ($authenticated->expired_at && now()->gt($authenticated->expired_at)) {
            throw new CustomException('token 已过期', CustomCode::AuthError);
        }

        $authenticated->loadAuthenticatedUser();

        if (!$authenticated->authenticated_user) {
            throw new CustomException('认证者用户 不存在', CustomCode::AuthError);
        }

        $request->me = $authenticated->authenticated_user;

        return $next($request);
    }
}
