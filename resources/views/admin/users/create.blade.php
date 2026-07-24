@extends('layouts.vertical', ['title' => 'Create User'])

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Create New User</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User Management</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add New User Account</h5>
                </div><!-- end card header -->

                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. John Doe">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="e.g. john@example.com">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required placeholder="Minimum 8 characters">
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter password">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label mb-2">Assign Roles</label>
                            <div class="row g-2">
                                @forelse($roles as $role)
                                    <div class="col-md-4">
                                        <div class="card border border-light-subtle p-2 mb-0 shadow-none">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}">
                                                <label class="form-check-label fs-13 fw-semibold text-dark mb-0" for="role_{{ $role->id }}">
                                                    {{ $role->name }}
                                                </label>
                                                @if($role->description)
                                                    <span class="text-muted d-block fs-11 mt-0">{{ $role->description }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p class="text-muted fs-13 m-0">No roles available to assign.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light me-1">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
