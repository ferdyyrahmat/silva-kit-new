<?php

namespace App\Http\Controllers\System\Setting;

use App\Http\Controllers\Controller;
use App\Services\PusherBroadcasterService;
use Illuminate\Http\Request;

class WebSocketSettingController extends Controller
{
    protected PusherBroadcasterService $broadcaster;

    public function __construct(PusherBroadcasterService $broadcaster)
    {
        $this->broadcaster = $broadcaster;
    }

    public function index()
    {
        $settings = $this->broadcaster->getPusherSettings();
        return view('admin.settings.websocket', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'pusher_app_id'      => 'nullable|string',
            'pusher_app_key'     => 'nullable|string',
            'pusher_app_secret'  => 'nullable|string',
            'pusher_app_cluster' => 'nullable|string',
            'pusher_host'        => 'nullable|string',
            'pusher_port'        => 'nullable|string',
        ]);

        $this->broadcaster->savePusherSettings($request->all());
        audit_log("Updated WebSocket & Pusher configurations", 'update', 'settings');

        return response()->json([
            'success'  => true,
            'message'  => 'WebSocket & Pusher settings saved successfully!',
            'redirect' => route('admin.settings.websocket.index')
        ]);
    }

    public function test(Request $request)
    {
        $res = $this->broadcaster->broadcast('silva-test-channel', 'test-event', [
            'message'   => 'Live Pusher WebSocket test connection successful!',
            'timestamp' => now()->toDateTimeString(),
            'sender'    => auth()->user()?->name ?? 'System Admin'
        ]);

        return response()->json([
            'success' => $res['success'],
            'message' => $res['message'],
            'redirect' => route('admin.settings.websocket.index')
        ]);
    }
}
