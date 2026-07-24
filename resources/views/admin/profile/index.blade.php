@extends('layouts.vertical', ['title' => 'My Account'])

@section('css')
<style>
    .avatar-wrapper {
        position: relative;
        display: inline-block;
        width: 110px;
        height: 110px;
        flex-shrink: 0;
    }
    .profile-avatar-img {
        width: 110px !important;
        height: 110px !important;
        object-fit: cover !important;
        object-position: center !important;
        border-radius: 50% !important;
        aspect-ratio: 1 / 1 !important;
    }
    .avatar-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #008080;
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid #fff;
        transition: all 0.2s ease;
    }
    .avatar-overlay:hover {
        transform: scale(1.1);
        background: #005f5f;
    }
    .fs-11 {
        font-size: 0.75rem;
    }
    .fs-13 {
        font-size: 0.85rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">My Account</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">My Account</li>
            </ol>
        </div>
    </div>

    <!-- Profile Header Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center flex-column flex-md-row gap-4">
                        <div class="avatar-wrapper">
                            <img src="{{ $user->avatar_url }}" id="profile-avatar-preview" class="profile-avatar-img img-thumbnail shadow" alt="user-avatar">
                            <label for="avatar-file-input" class="avatar-overlay" title="Upload New Avatar">
                                <i class="mdi mdi-camera fs-16"></i>
                            </label>
                        </div>
                        <div class="text-center text-md-start flex-grow-1">
                            <h3 class="fw-bold text-dark mb-1" id="profile-name-header">{{ $user->name }}</h3>
                            <p class="text-muted fs-14 mb-2" id="profile-email-header"><i class="mdi mdi-email-outline me-1"></i>{{ $user->email }}</p>
                            
                            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center justify-content-md-start mb-2">
                                <span class="badge bg-soft-primary text-primary px-3 py-1 fs-12 fw-semibold" id="profile-title-header">
                                    <i class="mdi mdi-briefcase-outline me-1"></i>{{ $user->title ?? 'Team Member' }}
                                </span>
                                @forelse($user->roles as $role)
                                    <span class="badge bg-light text-primary px-2 py-1 fs-11 border border-primary-subtle">
                                        <i class="mdi mdi-shield-check-outline me-1"></i>{{ $role->name }}
                                    </span>
                                @empty
                                    <span class="badge bg-light text-muted fs-11">No Role</span>
                                @endforelse
                            </div>

                            <div class="text-muted fs-12">
                                <i class="mdi mdi-calendar-clock me-1"></i>Member since {{ $user->created_at ? $user->created_at->format('F Y') : '-' }}
                            </div>
                        </div>

                        <div class="d-flex gap-3 text-center border-start-md ps-md-4">
                            <div>
                                <h4 class="fw-bold mb-0 text-primary">{{ $user->roles->count() }}</h4>
                                <span class="text-muted fs-12">Roles</span>
                            </div>
                            <div class="border-start pe-1 ps-3">
                                <h4 class="fw-bold mb-0 text-info">{{ $groupedPermissions->flatten()->count() }}</h4>
                                <span class="text-muted fs-12">Allowed Routes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs & Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body pt-0">
                    <ul class="nav nav-underline border-bottom pt-2" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active p-2" data-bs-toggle="tab" href="#tab-personal" role="tab">
                                Personal Details
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link p-2" data-bs-toggle="tab" href="#tab-security" role="tab">
                                Security & Password
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link p-2" data-bs-toggle="tab" href="#tab-permissions" role="tab">
                                My Permissions Matrix
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content text-muted bg-white pt-3">
                        
                        <!-- TAB 1: Personal Details -->
                        <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">
                            <form id="profile-info-form" action="{{ route('v1.profile.update-info') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <input type="file" id="avatar-file-input" name="avatar" class="d-none" accept="image/*">

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="name" class="form-label fw-medium">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required placeholder="Your full name">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-medium">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required placeholder="Your email address">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="phone" class="form-label fw-medium">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" placeholder="e.g. +62 812 3456 7890">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="title" class="form-label fw-medium">Job Title / Designation</label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ $user->title }}" placeholder="e.g. Senior Developer, System Admin">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="bio" class="form-label fw-medium">Bio / About Me</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3" placeholder="Write a short summary about your role or background">{{ $user->bio }}</textarea>
                                </div>

                                <div class="text-end">
                                    <button type="submit" id="btn-save-info" class="btn btn-primary">
                                        <i class="mdi mdi-content-save-outline me-1"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- TAB 2: Security & Password -->
                        <div class="tab-pane fade" id="tab-security" role="tabpanel">
                            <form id="profile-password-form" action="{{ route('v1.profile.update-password') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-6 offset-md-3 mb-3">
                                        <label for="current_password" class="form-label fw-medium">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required placeholder="Enter current password">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 offset-md-3 mb-3">
                                        <label for="password" class="form-label fw-medium">New Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required placeholder="Minimum 8 characters">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6 offset-md-3">
                                        <label for="password_confirmation" class="form-label fw-medium">Confirm New Password</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter new password">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3 text-end">
                                        <button type="submit" id="btn-save-password" class="btn btn-primary">
                                            <i class="mdi mdi-key-change me-1"></i>Update Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- TAB 3: Permissions Matrix -->
                        <div class="tab-pane fade" id="tab-permissions" role="tabpanel">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5 class="fw-semibold mb-1">Granted Access Matrix</h5>
                                    <p class="text-muted fs-13">Below are all the active routes and modules granted to your account based on your assigned roles.</p>
                                </div>
                            </div>

                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                @forelse($groupedPermissions as $group => $perms)
                                    <div class="col">
                                        <div class="card h-100 border border-light-subtle shadow-none">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="m-0 fw-semibold text-primary"><i class="mdi mdi-folder-lock-outline me-1"></i>{{ $group }}</h6>
                                            </div>
                                            <div class="card-body py-2">
                                                <ul class="list-group list-group-flush">
                                                    @foreach($perms as $p)
                                                        <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <span class="fw-semibold text-dark fs-13">{{ $p->name }}</span>
                                                                <span class="text-muted d-block fs-11">{{ $p->route_name }}</span>
                                                            </div>
                                                            <span class="badge bg-light text-success fs-11"><i class="mdi mdi-check-circle-outline me-1"></i>Allowed</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-4">
                                        <i class="mdi mdi-shield-alert-outline text-muted fs-36"></i>
                                        <p class="text-muted fs-14 mt-2">No route permissions assigned to your account.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-bottom')
<script>
    $(document).ready(function() {
        // Preview selected avatar image
        $('#avatar-file-input').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#profile-avatar-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        // Submit Personal Details form via AJAX
        $('#profile-info-form').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var btn = $('#btn-save-info');

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline me-1"></i>Save Changes');
                    if (response.success) {
                        Swal.fire('Saved!', response.message, 'success');
                        if (response.user) {
                            $('#profile-name-header').text(response.user.name);
                            $('#profile-email-header').html('<i class="mdi mdi-email-outline me-1"></i>' + response.user.email);
                            $('#profile-title-header').html('<i class="mdi mdi-briefcase-outline me-1"></i>' + response.user.title);
                            if (response.user.avatar_url) {
                                $('#profile-avatar-preview').attr('src', response.user.avatar_url);
                            }
                        }
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline me-1"></i>Save Changes');
                    var msg = xhr.responseJSON?.message || 'Failed to update profile information.';
                    Swal.fire('Error!', msg, 'error');
                }
            });
        });

        // Submit Security Password form via AJAX
        $('#profile-password-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var btn = $('#btn-save-password');

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    btn.prop('disabled', false).html('<i class="mdi mdi-key-change me-1"></i>Update Password');
                    if (response.success) {
                        Swal.fire('Updated!', response.message, 'success');
                        form[0].reset();
                    } else {
                        Swal.fire('Failed!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="mdi mdi-key-change me-1"></i>Update Password');
                    var msg = xhr.responseJSON?.message || 'Failed to update password.';
                    Swal.fire('Error!', msg, 'error');
                }
            });
        });
    });
</script>
@endsection
