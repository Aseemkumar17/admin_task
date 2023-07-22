<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomAuthException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


use Illuminate\Support\Facades\Auth;

class AdminProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::user()->can('admin-profile')) {
            return response()->json(
                ['success'=>false,
                    'message'=>'You are not authorised to access this, contact admin for further details.']
                ,403);
        }
        return $next($request);
    }
}
