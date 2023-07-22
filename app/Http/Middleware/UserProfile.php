<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomAuthException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


use Illuminate\Support\Facades\Auth;
class UserProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::user()->can('user-profile')) {
            return response()->json(
                ['success'=>false,
                    'message'=>'You are not authorised to access this,']
                ,403);
        
        }
        return $next($request);
    }
}
