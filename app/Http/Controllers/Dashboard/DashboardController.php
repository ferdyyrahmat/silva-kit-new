<?php

namespace App\Http\Controllers\Dashboard;

use App\Services\SystemHealthService;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class DashboardController extends BaseController
{
    public function index(SystemHealthService $healthService)
    {
        $healthMetrics = $healthService->getHealthMetrics();
        $totalUsers = \App\Models\User::count();
        $activeRoles = \App\Models\Role::count();
        $recentAuditLogs = \App\Models\AuditLog::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard.index', compact('healthMetrics', 'totalUsers', 'activeRoles', 'recentAuditLogs'));
    }
}