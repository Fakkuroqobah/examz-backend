<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class AssignGuard extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        try {
            JWTAuth::parseToken()->authenticate();
            $user = auth($guard)->user();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status' => 'Token tidak valid']);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status' => 'Token telah kadaluarsa']);
            }else{
                return response()->json(['status' => 'Otentikasi gagal']);
            }
        }
        
        if ($user && ($user->role == $guard)) {
            return $next($request);
        }
    
        return $this->unauthorized();
    }
    
    private function unauthorized($message = null){
        return response()->json([
            'message' => $message ? $message : 'Kamu tidak memiliki akses kedalam sumber ini',
            'success' => false
        ], 401);
    }
}
