<?php

namespace App\Http\Controllers\System\User;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = User::query()->with('roles');
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return e($row->name);
                })
                ->editColumn('email', function ($row) {
                    return e($row->email);
                })
                ->addColumn('roles', function ($row) {
                    $roleNames = $row->roles->pluck('name')->toArray();
                    $rolesBadges = '';
                    if (empty($roleNames)) {
                        $rolesBadges = '<span class="badge bg-light text-muted fs-11">No Role</span>';
                    } else {
                        foreach ($roleNames as $name) {
                            $rolesBadges .= '<span class="badge bg-light text-primary fs-11 me-1">' . e($name) . '</span>';
                        }
                    }
                    return $rolesBadges;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '-';
                })
                ->addColumn('actions', function ($row) {
                    $editUrl = route('admin.users.edit', $row->id);
                    $deleteUrl = route('admin.users.destroy', $row->id);
                    return '
                        <div class="text-center">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i class="mdi mdi-square-edit-outline fs-16"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteUser(' . $row->id . ', \'' . $deleteUrl . '\')">
                                <i class="mdi mdi-trash-can-outline fs-16"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['roles', 'actions'])
                ->make(true);
        }
        
        return view('admin.users.index');
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        \App\Models\AuditLog::log('user.create', "Created user '{$user->name}' ({$user->email})", 'user', ['user_id' => $user->id]);

        // Send welcome notification to created user
        $roleNames = $user->roles->pluck('name')->join(', ') ?: 'User';
        \App\Models\SystemNotification::send(
            $user,
            'Account Created',
            "Your account has been created with role(s): {$roleNames}.",
            'success',
            'mdi-account-check-outline',
            route('v1.profile.index')
        );

        return response()->json([
            'success' => true,
            'message' => 'User created successfully!',
            'redirect' => route('admin.users.index')
        ]);
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $userRoleIds = $user->roles->pluck('id')->toArray();
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'userRoleIds', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldRoleIds = $user->roles->pluck('id')->sort()->values()->toArray();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        $newRoles = $request->roles ?? [];
        $user->roles()->sync($newRoles);

        $user->load('roles');
        $newRoleNames = $user->roles->pluck('name')->join(', ') ?: 'No Role';

        \App\Models\AuditLog::log('user.update', "Updated user '{$user->name}'", 'user', [
            'target_user_id' => $user->id,
            'roles' => $newRoleNames
        ]);

        // Send Bell Notification to the target user if roles were changed or account updated
        \App\Models\SystemNotification::send(
            $user,
            'Role & Profile Updated',
            "Your profile/role has been updated by administrator. Your assigned role(s): {$newRoleNames}.",
            'role_update',
            'mdi-shield-account-outline',
            route('v1.profile.index')
        );

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!',
            'redirect' => route('admin.users.index')
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $deletedName = $user->name;
        $user->delete();

        \App\Models\AuditLog::log('user.delete', "Deleted user '{$deletedName}'", 'user', ['user_id' => $id]);

        return response()->json([
            'success'  => true,
            'message'  => 'User deleted successfully!',
            'redirect' => route('admin.users.index')
        ]);
    }
}
