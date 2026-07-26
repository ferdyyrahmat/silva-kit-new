<?php

namespace App\Http\Controllers\System\Notification;

use App\Http\Controllers\Controller;
use App\Models\NotificationBlast;
use App\Models\Role;
use App\Models\User;
use App\Services\NotificationConnectorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationConnectorService $connectorService;

    public function __construct(NotificationConnectorService $connectorService)
    {
        $this->connectorService = $connectorService;
    }

    public function index()
    {
        $blasts = NotificationBlast::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::orderBy('name')->get();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $connectorSettings = $this->connectorService->getConnectorSettings();

        return view('admin.notification.index', compact('blasts', 'roles', 'users', 'connectorSettings'));
    }

    public function storeSettings(Request $request)
    {
        $this->connectorService->saveConnectorSettings($request->all());

        audit_log('Updated Notification Connectors Settings', 'update', 'notification');

        $message = 'Notification connector settings saved successfully!';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('admin.notifications.index')
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'redirect' => route('admin.notifications.index')
        ]);
    }

    public function testConnector(Request $request)
    {
        $channel   = $request->input('channel');
        $recipient = $request->input('recipient');
        $token     = $request->input('token');
        $apiUrl    = $request->input('api_url');
        $provider  = $request->input('provider');

        if ($channel === 'email') {
            $res = $this->connectorService->testEmailConnection($recipient ?? Auth::user()->email);
        } elseif ($channel === 'whatsapp') {
            $res = $this->connectorService->testWhatsAppConnection($recipient ?? Auth::user()->phone ?? '6289524424936', $token, $apiUrl, $provider);
        } elseif ($channel === 'telegram') {
            $res = $this->connectorService->testTelegramConnection($recipient ?? '', $token);
        } else {
            $res = ['success' => false, 'message' => 'Invalid channel specified.'];
        }

        return response()->json($res, $res['success'] ? 200 : 422);
    }

    public function sendBlast(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'message'     => 'required|string',
            'channels'    => 'required|array|min:1',
            'target_type' => 'required|in:all,role,user',
            'target_id'   => 'nullable|integer',
            'type'        => 'required|in:info,success,warning,danger',
        ]);

        $targetType = $request->input('target_type');
        $targetId   = $request->input('target_id');
        $channels   = $request->input('channels');
        $title      = $request->input('title');
        $message    = $request->input('message');
        $type       = $request->input('type');
        $url        = $request->input('url');

        if ($targetType === 'role') {
            $request->validate(['target_id' => 'required|exists:roles,id']);
        } elseif ($targetType === 'user') {
            $request->validate(['target_id' => 'required|exists:users,id']);
        } else {
            $targetId = null;
        }

        // Determine target users query
        if ($targetType === 'role' && $targetId) {
            $targetUsers = User::whereHas('roles', function ($q) use ($targetId) {
                $q->where('roles.id', $targetId);
            })->get();
        } elseif ($targetType === 'user' && $targetId) {
            $targetUsers = User::where('id', $targetId)->get();
        } else {
            $targetUsers = User::all();
        }

        $sentCount   = 0;
        $failedCount = 0;

        foreach ($targetUsers as $user) {
            $res = $this->connectorService->sendToUser($user, $title, $message, $channels, $type, $url);
            if (array_sum($res) > 0) {
                $sentCount++;
            } else {
                $failedCount++;
            }
        }

        $blast = NotificationBlast::create([
            'title'        => $title,
            'message'      => $message,
            'channels'     => $channels,
            'target_type'  => $targetType,
            'target_id'    => $targetId,
            'type'         => $type,
            'status'       => 'sent',
            'sent_count'   => $sentCount,
            'failed_count' => $failedCount,
            'created_by'   => Auth::id(),
        ]);

        audit_log('Dispatched Notification Blast #' . $blast->id . ' to ' . count($targetUsers) . ' users', 'create', 'notification');

        $msg = "Notification Blast successfully dispatched to {$sentCount} recipient(s)!";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'blast'   => $blast,
                'redirect' => route('admin.notifications.index')
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'redirect' => route('admin.notifications.index')
        ]);
    }
}
