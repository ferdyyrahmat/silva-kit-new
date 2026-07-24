<?php

namespace App\Http\Controllers\System\Maintenance;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $status = SystemSetting::getByKey('maintenance_mode', false);
        $title = SystemSetting::getByKey('maintenance_title', 'Our website is currently under construction.');
        $message = SystemSetting::getByKey('maintenance_message', 'We sincerely apologize for the inconvenience. Our site is currently undergoing scheduled maintenance and upgrades, but will return shortly.');

        return view('admin.maintenance.index', compact('status', 'title', 'message'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        SystemSetting::setByKey('maintenance_mode', (bool) $request->status, 'boolean', 'Whether the application is in maintenance mode.');
        SystemSetting::setByKey('maintenance_title', $request->title, 'string', 'Title shown on maintenance page.');
        SystemSetting::setByKey('maintenance_message', $request->message, 'string', 'Message shown on maintenance page.');

        $statusStr = $request->status ? 'ON' : 'OFF';
        \App\Models\AuditLog::log('maintenance.update', "Updated maintenance settings (Status: {$statusStr})", 'maintenance', [
            'status' => $statusStr,
            'title'  => $request->title
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance settings updated successfully!',
            'redirect' => route('admin.maintenance.index')
        ]);
    }
}