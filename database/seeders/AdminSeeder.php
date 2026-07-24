<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find the Administrator role
        $role = Role::firstOrCreate(
            ['name' => 'Administrator'],
            [
                'description' => 'Full system access with all privileges.',
                'guard_name'  => 'web',
            ]
        );

        // Create or update admin user
        $user = User::updateOrCreate(
            ['email' => 'ferdyyrahmat@gmail.com'],
            [
                'name'     => 'Ferdy Rahmat',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign Administrator role to the user
        if (!$user->roles()->where('role_id', $role->id)->exists()) {
            $user->roles()->attach($role->id);
        }

        $this->command->info("✅ Admin user created: {$user->email} (password: password)");
    }
}
