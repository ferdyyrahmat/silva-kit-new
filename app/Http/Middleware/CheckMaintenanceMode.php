<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemSetting;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if maintenance mode is enabled
        $isMaintenance = SystemSetting::getByKey('maintenance_mode', false);

        if ($isMaintenance) {
            $routeName = $request->route() ? $request->route()->getName() : null;

            // Block access to registration and password reset paths during maintenance
            $blockedRoutes = ['register', 'register.store', 'password.request', 'password.email', 'password.reset', 'password.update'];
            if (in_array($routeName, $blockedRoutes)) {
                abort(503, 'Registration and password resets are unavailable during maintenance.');
            }

            // Check if the current route is login, authentication, logout, maintenance view or assets, to avoid loops and allow logging in
            $allowedRoutes = ['login', 'login.authenticate', 'maintenance.page', 'logout'];
            if (in_array($routeName, $allowedRoutes) || $request->is('images/*') || $request->is('assets/*') || $request->is('build/*')) {
                return $next($request);
            }

            // Exclude Administrator and RCID (roles check)
            $bypass = false;
            if (Auth::check()) {
                $user = Auth::user();
                $roles = $user->roles->pluck('name')->map(fn($item) => strtolower(trim($item)))->toArray();
                
                if (in_array('administrator', $roles) || in_array('rcid', $roles)) {
                    $bypass = true;
                }
            }

            if (!$bypass) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'System under maintenance.'
                    ], 503);
                }

                return response()->view('error.maintenance', [], 503);
            }
        }

        return $next($request);
    }
}
