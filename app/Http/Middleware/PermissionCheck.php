<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\user_permission;
use App\Models\Access_permission;
class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string                   $permissionLevel
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissionLevel)
    {
        if (Auth::user()->role == 1) {
            return $next($request);
        } elseif (Auth::user()->role == 0) {
            $permission = Access_permission::where('permission', $permissionLevel)->first();
            $userPermission = user_permission::where([
                'user_id' => Auth::user()->id,
                'permission_id' => $permission->id,
            ])->first();
            if ($userPermission) {
                return $next($request);
            } else {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
        }
    }
}