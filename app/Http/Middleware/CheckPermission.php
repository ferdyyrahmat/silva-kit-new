<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $routeName = $request->route()->getName();

        if ($routeName) {
            $permissionExists = Permission::where('route_name', $routeName)->exists();

            if ($permissionExists) {
                if (!$user->hasPermission($routeName)) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized: You do not have permission to perform this action.'
                        ], 403);
                    }
                    abort(403, 'Unauthorized access - Permission Denied');
                }
            }
        }

        return $next($request);
    }
}
