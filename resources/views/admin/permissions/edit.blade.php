@extends('layouts.vertical', ['title' => 'Edit Role & Permissions'])

@section('css')
<style>
    .transition-all {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }
    .fs-9 {
        font-size: 0.65rem;
    }
    .fs-11 {
        font-size: 0.75rem;
    }
    .fs-13 {
        font-size: 0.85rem;
    }
    .user-scroll-container {
        max-height: 220px;
        overflow-y: auto;
        padding-right: 5px;
    }
    .user-scroll-container::-webkit-scrollbar {
        width: 6px;
    }
    .user-scroll-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .user-scroll-container::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }
    .user-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Edit Role & Permissions</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Roles & Permissions</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>

    <form action="{{ route('admin.permissions.update', $role->id) }}" method="POST" class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Role Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <input type="text" class="form-control" id="name" name="name" required value="{{ $role->name }}" placeholder="e.g. Administrator, Editor, Supervisor" {{ $role->isLocked() ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="description" name="description" value="{{ $role->description }}" placeholder="Short description of the role's purpose">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <h5 class="card-title mb-0">Assign Users to this Role</h5>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" id="user-search" class="form-control form-control-sm" style="width: 220px;" placeholder="Filter users by name/email...">
                        </div>
                    </div>
                    <div class="card-body py-3">
                        <div class="user-scroll-container">
                            <div class="row g-2">
                                @forelse($users as $u)
                                    <div class="col-md-4 user-col">
                                        <div class="card border border-light-subtle p-2 mb-0 shadow-none transition-all hover-shadow user-item-card">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" name="users[]" value="{{ $u->id }}" id="user_{{ $u->id }}" {{ in_array($u->id, $roleUserIds) ? 'checked' : '' }}>
                                                <label class="form-check-label fs-13 fw-semibold text-dark mb-0" for="user_{{ $u->id }}">
                                                    {{ $u->name }}
                                                </label>
                                                <span class="text-muted d-block fs-11 mt-0">{{ $u->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-center py-2">
                                        <p class="text-muted fs-13 m-0">No users found in database.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3 align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <h5 class="m-0 fw-semibold">Assign Route Permissions</h5>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-2 justify-content-md-end">
                    <input type="text" id="permission-search" class="form-control form-control-sm w-50" placeholder="Filter permissions (route name, suffix, URI)...">
                    <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap" id="check-all-global">Toggle Select All Global</button>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 mb-4">
            @foreach($groupedRoutes as $group => $routes)
                <div class="col">
                    <div class="card h-100 border border-light-subtle shadow-sm transition-all hover-shadow">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                            <h6 class="m-0 fw-semibold text-primary"><i class="mdi mdi-folder-outline me-1"></i>{{ $group }}</h6>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input select-all-group" type="checkbox" id="select_all_{{ Str::slug($group) }}" data-group="{{ Str::slug($group) }}">
                                <label class="form-check-label fs-11 text-muted" for="select_all_{{ Str::slug($group) }}">Select All</label>
                            </div>
                        </div>
                        <div class="card-body py-3">
                            @foreach($routes as $route)
                                <div class="form-check mb-2">
                                    <input class="form-check-input group-item-{{ Str::slug($group) }}" type="checkbox" name="permissions[]" value="{{ $route['name'] }}" id="perm_{{ Str::slug($route['name']) }}" {{ in_array($route['name'], $rolePermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label fs-13" for="perm_{{ Str::slug($route['name']) }}">
                                        <strong class="text-dark">{{ $route['suffix'] }}</strong> 
                                        <span class="text-muted d-block fs-11">{{ $route['uri'] }}</span>
                                        <span class="badge bg-light text-muted fs-9">{{ $route['method'] }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mb-4">
            <div class="col-12 text-end">
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-light me-1">Cancel</a>
                @if($role->isLocked())
                    <span class="badge bg-warning-subtle text-warning me-2"><i class="mdi mdi-lock-outline me-1"></i>{{ $role->name }} locked for deletion</span>
                @endif
                <button type="submit" class="btn btn-primary">Update Role & Permissions</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script-bottom')
<script>
    $(document).ready(function() {
        $('.select-all-group').each(function() {
            var group = $(this).data('group');
            var allChecked = $('.group-item-' + group).length === $('.group-item-' + group + ':checked').length;
            $(this).prop('checked', allChecked);
        });

        $('.select-all-group').on('change', function() {
            var group = $(this).data('group');
            $('.group-item-' + group).prop('checked', this.checked);
        });

        $('input[name="permissions[]"]').on('change', function() {
            var classes = $(this).attr('class').split(' ');
            var groupClass = classes.find(function(c) { return c.startsWith('group-item-'); });
            if (groupClass) {
                var group = groupClass.replace('group-item-', '');
                var allChecked = $('.group-item-' + group).length === $('.group-item-' + group + ':checked').length;
                $('#select_all_' + group).prop('checked', allChecked);
            }
        });

        // Toggle Select All Global
        var globalState = false;
        $('#check-all-global').on('click', function() {
            globalState = !globalState;
            $('input[name="permissions[]"]').prop('checked', globalState);
            $(this).text(globalState ? 'Deselect All Global' : 'Select All Global');
        });

        // Real-time Permission Filter Search
        $('#permission-search').on('input', function() {
            var query = $(this).val().toLowerCase();
            
            $('.row-cols-1 .card').each(function() {
                var card = $(this);
                var visibleItems = 0;
                
                card.find('.form-check').each(function() {
                    var item = $(this);
                    var text = item.text().toLowerCase();
                    var value = item.find('input').val().toLowerCase();
                    
                    if (text.includes(query) || value.includes(query)) {
                        item.show();
                        visibleItems++;
                    } else {
                        item.hide();
                    }
                });
                
                if (visibleItems > 0 || query === '') {
                    card.parent().show();
                } else {
                    card.parent().hide();
                }
            });
        });
        // Real-time User Search Filter
        $('#user-search').on('input', function() {
            var query = $(this).val().toLowerCase();
            $('.user-item-card').each(function() {
                var item = $(this);
                var text = item.text().toLowerCase();
                if (text.includes(query) || query === '') {
                    item.closest('.user-col').show();
                } else {
                    item.closest('.user-col').hide();
                }
            });
        });
    });
</script>
@endsection
