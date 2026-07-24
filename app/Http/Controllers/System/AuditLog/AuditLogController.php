<?php

namespace App\Http\Controllers\System\AuditLog;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = AuditLog::query()->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '<span class="fw-semibold text-dark">' . e($row->user_name ?? 'System/Guest') . '</span>';
                })
                ->editColumn('event', function ($row) {
                    $badgeClass = match (true) {
                        str_contains($row->event, 'login')    => 'bg-success',
                        str_contains($row->event, 'logout')   => 'bg-secondary',
                        str_contains($row->event, 'create')   => 'bg-info',
                        str_contains($row->event, 'update')   => 'bg-primary',
                        str_contains($row->event, 'delete')   => 'bg-danger',
                        default                               => 'bg-dark',
                    };
                    return '<span class="badge ' . $badgeClass . ' fs-11">' . e($row->event) . '</span>';
                })
                ->editColumn('action_description', function ($row) {
                    return html_entity_decode($row->action_description, ENT_QUOTES, 'UTF-8');
                })
                ->editColumn('module', function ($row) {
                    return '<span class="badge bg-light text-primary border fs-11 text-capitalize">' . e($row->module) . '</span>';
                })
                ->editColumn('ip_address', function ($row) {
                    return '<code class="fs-12">' . e($row->ip_address ?? 'N/A') . '</code>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-';
                })
                ->rawColumns(['user_name', 'event', 'action_description', 'module', 'ip_address'])
                ->make(true);
        }

        return view('admin.audit-logs.index');
    }
}
