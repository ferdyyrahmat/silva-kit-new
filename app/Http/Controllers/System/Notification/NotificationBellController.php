<?php

namespace App\Http\Controllers\System\Notification;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationBellController extends Controller
{
    public function getNotifications()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unreadCount' => 0, 'notifications' => []]);
        }

        $notifications = SystemNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id'         => $item->id,
                    'title'      => e($item->title),
                    'message'    => e($item->message),
                    'type'       => $item->type,
                    'icon'       => $item->icon ?? 'mdi-bell-outline',
                    'url'        => $item->url ?? 'javascript:void(0);',
                    'is_read'    => $item->is_read,
                    'time_ago'   => $item->created_at ? $item->created_at->diffForHumans() : 'Just now',
                ];
            });

        $unreadCount = SystemNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unreadCount'   => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = SystemNotification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = SystemNotification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function clearAll()
    {
        SystemNotification::where('user_id', Auth::id())->delete();

        return response()->json(['success' => true]);
    }
}
