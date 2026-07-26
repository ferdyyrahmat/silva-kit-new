<?php

namespace App\Http\Controllers\Dashboard;

use App\Services\SystemHealthService;
use App\Models\AuditLog;
use App\Models\Developer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class DashboardController extends BaseController
{
    public function index(SystemHealthService $healthService)
    {
        $user = Auth::user();

        if ($user->isDeveloper()) {
            $developer = Developer::where('user_id', $user->id)->first();
            $tickets = $developer
                ? Ticket::where('assigned_developer_id', $developer->id)
                : Ticket::whereRaw('1 = 0');

            return view('dashboard.developer', [
                'user' => $user,
                'healthMetrics' => $healthService->getHealthMetrics(),
                'stats' => [
                    'open' => (clone $tickets)->where('status', 'open')->count(),
                    'in_progress' => (clone $tickets)->where('status', 'in_progress')->count(),
                    'waiting' => (clone $tickets)->where('status', 'waiting_user')->count(),
                    'resolved' => (clone $tickets)->whereIn('status', ['resolved', 'closed'])->count(),
                ],
                'recentTickets' => (clone $tickets)->with('user')->latest()->take(6)->get(),
            ]);
        }

        if ($user->isAdmin()) {
            return view('dashboard.admin', [
                'user' => $user,
                'healthMetrics' => $healthService->getHealthMetrics(),
                'stats' => [
                    'users' => User::count(),
                    'tickets' => Ticket::count(),
                    'open_tickets' => Ticket::whereIn('status', ['open', 'in_progress', 'waiting_user'])->count(),
                    'resolved_tickets' => Ticket::whereIn('status', ['resolved', 'closed'])->count(),
                ],
                'recentAuditLogs' => AuditLog::with('user')->latest()->take(6)->get(),
                'recentTickets' => Ticket::with('assignedDeveloper')->latest()->take(6)->get(),
            ]);
        }

        $myTickets = Ticket::where('user_id', $user->id);

        return view('dashboard.user', [
            'user' => $user,
            'stats' => [
                'total' => (clone $myTickets)->count(),
                'active' => (clone $myTickets)->whereIn('status', ['open', 'in_progress', 'waiting_user'])->count(),
                'resolved' => (clone $myTickets)->whereIn('status', ['resolved', 'closed'])->count(),
                'unread' => $user->notifications()->where('is_read', false)->count(),
            ],
            'recentTickets' => (clone $myTickets)->latest()->take(6)->get(),
        ]);

    }
}
