<?php

namespace App\Http\Controllers\System\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function index()
    {
        return view('admin.permissions.index');
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function edit($id)
    {
        return view('admin.permissions.edit', compact('id'));
    }
}