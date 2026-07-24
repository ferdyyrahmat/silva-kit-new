<?php

namespace App\Http\Controllers\System\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['success' => true, 'results' => []]);
        }

        $results = [];

        // 1. Search Navigation Pages & System Modules
        $pages = [
            ['name' => __('messages.dashboard') . ' / Dashboard', 'url' => route('v1.dashboard'), 'icon' => 'mdi-view-dashboard-outline'],
            ['name' => __('messages.user_management') . ' / User Management', 'url' => route('admin.users.index'), 'icon' => 'mdi-account-group-outline'],
            ['name' => __('messages.roles_permissions') . ' / Roles & Permissions', 'url' => route('admin.permissions.index'), 'icon' => 'mdi-shield-key-outline'],
            ['name' => __('messages.my_account') . ' / My Account', 'url' => route('v1.profile.index'), 'icon' => 'mdi-account-circle-outline'],
            ['name' => 'Maintenance', 'url' => route('admin.maintenance.index'), 'icon' => 'mdi-cloud-off-outline'],
            ['name' => 'Notification Blast', 'url' => route('admin.notifications.index'), 'icon' => 'mdi-bell-outline'],
            ['name' => 'Feedbacks', 'url' => route('admin.feedbacks.index'), 'icon' => 'mdi-inbox-outline'],
        ];

        foreach ($pages as $page) {
            if (stripos($page['name'], $query) !== false) {
                $results[] = [
                    'type' => 'page',
                    'category' => 'Pages & Features',
                    'title' => explode(' / ', $page['name'])[0],
                    'subtitle' => 'Navigation Page',
                    'url' => $page['url'],
                    'icon' => $page['icon']
                ];
            }
        }

        // 2. Search Users
        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(5)
            ->get();

        foreach ($users as $user) {
            $results[] = [
                'type' => 'user',
                'category' => 'Users',
                'title' => $user->name,
                'subtitle' => $user->email,
                'url' => route('admin.users.edit', $user->id),
                'avatar' => $user->avatar_url,
                'icon' => 'mdi-account-outline'
            ];
        }

        // 3. Search Roles
        if (class_exists(Role::class)) {
            $roles = Role::where('name', 'like', "%{$query}%")
                ->limit(5)
                ->get();

            foreach ($roles as $role) {
                $results[] = [
                    'type' => 'role',
                    'category' => 'Roles & Permissions',
                    'title' => $role->name,
                    'subtitle' => 'System Role',
                    'url' => route('admin.permissions.edit', $role->id),
                    'icon' => 'mdi-shield-outline'
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => array_values($results)
        ]);
    }
}
