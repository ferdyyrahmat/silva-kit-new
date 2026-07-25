<?php

namespace App\Http\Controllers\System\Permission;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Role::query()->withCount(['permissions', 'users']);
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return '<span class="badge bg-light text-primary fs-12">' . e($row->name) . '</span>';
                })
                ->editColumn('description', function ($row) {
                    return e($row->description ?? '-');
                })
                ->editColumn('permissions_count', function ($row) {
                    return '<span class="badge bg-light text-info fs-12">' . $row->permissions_count . ' Routes</span>';
                })
                ->editColumn('users_count', function ($row) {
                    return '<span class="badge bg-light text-success fs-12">' . $row->users_count . ' Users</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-';
                })
                ->addColumn('actions', function ($row) {
                    $editUrl = route('admin.permissions.edit', $row->id);
                    $deleteUrl = route('admin.permissions.destroy', $row->id);
                    return '
                        <div class="text-center">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="mdi mdi-square-edit-outline fs-16"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteRole(' . $row->id . ', \'' . $deleteUrl . '\')">
                                <i class="mdi mdi-trash-can-outline fs-16"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['name', 'permissions_count', 'users_count', 'actions'])
                ->make(true);
        }
        
        return view('admin.permissions.index');
    }

    public function create()
    {
        $groupedRoutes = $this->getGroupedRoutes();
        $users = User::all();
        return view('admin.permissions.create', compact('groupedRoutes', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => 'web'
        ]);

        if ($request->has('permissions')) {
            $permissionIds = [];
            foreach ($request->permissions as $routeName) {
                $parts = explode('.', $routeName);
                array_pop($parts);
                $group = implode('.', $parts);

                $permission = Permission::firstOrCreate(
                    ['route_name' => $routeName],
                    [
                        'name' => Str::title(str_replace(['.', '-', '_'], ' ', $routeName)),
                        'group_name' => $group,
                        'guard_name' => 'web'
                    ]
                );
                $permissionIds[] = $permission->id;
            }
            $role->permissions()->sync($permissionIds);
        }

        if ($request->has('users')) {
            $role->users()->sync($request->users);
        }

        \App\Models\AuditLog::log('role.create', "Created role '{$role->name}'", 'role');

        // Notify assigned users
        foreach ($role->users as $u) {
            \App\Models\SystemNotification::send(
                $u,
                'Role Assigned',
                "You have been assigned to role: {$role->name}.",
                'role_update',
                'mdi-shield-check-outline',
                route('v1.profile.index')
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Role and Permissions created successfully!',
            'redirect' => route('admin.permissions.index')
        ]);
    }

    public function edit($id)
    {
        $role = Role::with('users')->findOrFail($id);
        $rolePermissions = $role->permissions->pluck('route_name')->toArray();
        $roleUserIds = $role->users->pluck('id')->toArray();
        $groupedRoutes = $this->getGroupedRoutes();
        $users = User::all();

        return view('admin.permissions.edit', compact('role', 'rolePermissions', 'roleUserIds', 'groupedRoutes', 'users'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $permissionIds = [];
        if ($request->has('permissions')) {
            foreach ($request->permissions as $routeName) {
                $parts = explode('.', $routeName);
                array_pop($parts);
                $group = implode('.', $parts);

                $permission = Permission::firstOrCreate(
                    ['route_name' => $routeName],
                    [
                        'name' => Str::title(str_replace(['.', '-', '_'], ' ', $routeName)),
                        'group_name' => $group,
                        'guard_name' => 'web'
                    ]
                );
                $permissionIds[] = $permission->id;
            }
        }
        $role->permissions()->sync($permissionIds);

        $role->users()->sync($request->users ?? []);
        $role->load('users');

        \App\Models\AuditLog::log('role.update', "Updated role '{$role->name}' permissions/users", 'role');

        // Notify all users in this role
        foreach ($role->users as $u) {
            \App\Models\SystemNotification::send(
                $u,
                'Role & Permissions Updated',
                "Your role '{$role->name}' or its granted permissions have been updated.",
                'role_update',
                'mdi-shield-sync-outline',
                route('v1.profile.index')
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Role and Permissions updated successfully!',
            'redirect' => route('admin.permissions.index')
        ]);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $roleName = $role->name;
        $role->delete();

        \App\Models\AuditLog::log('role.delete', "Deleted role '{$roleName}'", 'role');

        return response()->json([
            'success'  => true,
            'message'  => 'Role deleted successfully!',
            'redirect' => route('admin.permissions.index')
        ]);
    }

    private function getGroupedRoutes()
    {
        $routes = Route::getRoutes();
        $groupedRoutes = [];

        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name && (Str::startsWith($name, 'admin.') || Str::startsWith($name, 'v1.'))) {
                // Skip profile and dashboard routes which are accessible to all users
                if (Str::startsWith($name, 'v1.profile.') || $name === 'v1.dashboard') {
                    continue;
                }

                $parts = explode('.', $name);
                
                if (count($parts) >= 3) {
                    $suffix = array_pop($parts);
                    $group = implode('.', $parts);
                } elseif (count($parts) == 2) {
                    $group = $name;
                    $suffix = 'index';
                } else {
                    continue;
                }

                $groupedRoutes[$group][] = [
                    'name' => $name,
                    'suffix' => $suffix,
                    'uri' => $route->uri(),
                    'method' => implode('|', $route->methods())
                ];
            }
        }
        ksort($groupedRoutes);
        return $groupedRoutes;
    }
}