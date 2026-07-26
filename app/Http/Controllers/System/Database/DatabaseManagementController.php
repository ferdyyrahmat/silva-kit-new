<?php

namespace App\Http\Controllers\System\Database;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\User;

class DatabaseManagementController extends BaseController
{
    public function index()
    {
        $this->ensureDeveloper();

        $tables = $this->applicationTables()
            ->map(fn (string $table) => [
                'name' => $table,
                'rows' => DB::table($table)->count(),
            ])
            ->values();

        return view('admin.database.index', [
            'tables' => $tables,
        ]);
    }

    public function clear(Request $request): RedirectResponse
    {
        $this->ensureDeveloper();

        $available = $this->applicationTables();
        $all = $request->boolean('clear_all');
        $selected = $all ? $available->all() : $request->input('tables', []);

        abort_if(!$all && empty($selected), 422, 'Select at least one table.');
        abort_if($all && $request->input('confirmation') !== 'CLEAR ALL DATA', 422, 'Confirmation text is invalid.');

        $tables = collect($selected)->filter(fn ($table) => $available->contains($table))->values();
        abort_if($tables->isEmpty(), 422, 'No valid tables selected.');

        $developerRoleIds = Role::whereRaw('LOWER(name) = ?', ['developer'])->pluck('id');
        abort_if($developerRoleIds->isEmpty(), 422, 'Developer role is missing; clear operation cancelled for safety.');
        $developerUserIds = User::whereHas('roles', fn ($query) => $query->whereIn('roles.id', $developerRoleIds))->pluck('id');

        Schema::disableForeignKeyConstraints();
        try {
            foreach ($tables as $table) {
                if ($table === 'users') {
                    DB::table('users')->whereNotIn('id', $developerUserIds)->delete();
                } elseif ($table === 'roles') {
                    DB::table('roles')->whereNotIn('id', $developerRoleIds)->delete();
                } elseif ($table === 'role_user') {
                    DB::table('role_user')->whereNotIn('role_id', $developerRoleIds)->delete();
                } else {
                    DB::table($table)->truncate();
                }
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        return back()->with('success', $tables->count() . ' table(s) cleared successfully.');
    }

    private function ensureDeveloper(): void
    {
        abort_unless(Auth::check() && Auth::user()->isDeveloper(), 403);
    }

    private function applicationTables()
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $rows = DB::select('SHOW TABLES');
            $tables = collect($rows)->map(fn ($row) => array_values((array) $row)[0]);
        } else {
            $tables = collect(Schema::getTableListing(null, false));
        }

        return $tables
            ->filter(fn (string $table) => Schema::hasTable($table))
            ->unique()
            ->sort()
            ->values();
    }
}
