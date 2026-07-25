<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Level 1 Role: Developer (Highest Authority)
        $devRole = Role::firstOrCreate(
            ['name' => 'Developer'],
            [
                'description' => 'Level 1 Supreme Authority: Full system access, developer tools, task queues, Redis, backups, and ticket resolution.',
                'guard_name'  => 'web',
            ]
        );

        // 2. Create Level 2 Role: Admin (Management Authority)
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            [
                'description' => 'Level 2 Management Authority: Manage users, review tickets, view audit trails, and manage system feedbacks.',
                'guard_name'  => 'web',
            ]
        );

        // 3. Create Level 3 Role: User (Standard Account)
        $userRole = Role::firstOrCreate(
            ['name' => 'User'],
            [
                'description' => 'Level 3 Standard Account: Registered user with access to user dashboard, profile, and ticket submissions.',
                'guard_name'  => 'web',
            ]
        );

        // Auto-seed permissions from registered admin routes to Developer and Admin roles
        $adminAllowedGroups = ['users', 'tickets', 'feedbacks', 'audit-logs'];

        foreach (Route::getRoutes() as $r) {
            $name = $r->getName();
            if ($name && str_starts_with($name, 'admin.')) {
                $parts = explode('.', $name);
                $group = isset($parts[1]) ? ucfirst($parts[1]) : 'System';

                $p = Permission::firstOrCreate(
                    ['route_name' => $name],
                    ['name' => ucwords(str_replace('.', ' ', $name)), 'group_name' => $group]
                );

                // Developer gets ALL admin permissions
                $devRole->permissions()->syncWithoutDetaching([$p->id]);

                // Admin gets operational management permissions
                if (isset($parts[1]) && in_array($parts[1], $adminAllowedGroups)) {
                    $adminRole->permissions()->syncWithoutDetaching([$p->id]);
                }
            }
        }

        // Create or update supreme Developer user (Ferdy Rahmat)
        $devUser = User::updateOrCreate(
            ['email' => 'ferdyyrahmat@gmail.com'],
            [
                'name'              => 'Ferdy Rahmat',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Developer role
        if (!$devUser->roles()->where('role_id', $devRole->id)->exists()) {
            $devUser->roles()->syncWithoutDetaching([$devRole->id]);
        }

        // Also ensure Developer is registered in developers table for ticket assignment & alerts
        \App\Models\Developer::updateOrCreate(
            ['email' => $devUser->email],
            [
                'user_id'          => $devUser->id,
                'name'             => $devUser->name,
                'phone'            => $devUser->phone ?? '6289524424936',
                'notify_channels'  => ['in_app', 'email', 'whatsapp', 'telegram'],
                'is_active'        => true,
            ]
        );

        $this->command->info("✅ Hierarchical Roles (Developer > Admin > User) & Developer User seeded cleanly!");
    }
}
