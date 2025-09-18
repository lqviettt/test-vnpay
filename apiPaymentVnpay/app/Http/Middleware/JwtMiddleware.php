<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Illuminate\Http\Request;

class JwtMiddleware
{
    /**
     * handle
     *
     * @param  mixed $request
     * @param  mixed $next
     * @return void
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->session()->get('jwt_token');
        if ($token) {
            try {
                $user = JWTAuth::setToken($token)->authenticate();
                if ($user) {
                    \Illuminate\Support\Facades\Auth::login($user);
                    return $next($request);
                }
            } catch (\Exception $e) {
                // Token không hợp lệ hoặc hết hạn
            }
        }
        return redirect()->route('login');
    }
}
