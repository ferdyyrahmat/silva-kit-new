@extends('layouts.vertical', ['title' => 'Permissions'])

@section('css')
    @vite([
        'node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css',
        'node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'
    ])
@endsection

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Permissions</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Permissions</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Permissions List</h5>
                    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm">Add Permission</a>
                </div><!-- end card header -->

                <div class="card-body">
                    <table id="responsive-datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Guard Name</th>
                                <th>Created At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td><span class="badge bg-light text-primary fs-12">user-create</span></td>
                                <td>Ability to create new users in the system</td>
                                <td>web</td>
                                <td>2026-07-24 10:00:00</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.permissions.edit', 1) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="mdi mdi-square-edit-outline fs-16"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deletePermission(1)">
                                        <i class="mdi mdi-trash-can-outline fs-16"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><span class="badge bg-light text-primary fs-12">user-edit</span></td>
                                <td>Ability to edit existing user details</td>
                                <td>web</td>
                                <td>2026-07-24 10:05:00</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.permissions.edit', 2) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="mdi mdi-square-edit-outline fs-16"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deletePermission(2)">
                                        <i class="mdi mdi-trash-can-outline fs-16"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><span class="badge bg-light text-primary fs-12">user-delete</span></td>
                                <td>Ability to delete/deactivate users</td>
                                <td>web</td>
                                <td>2026-07-24 10:10:00</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.permissions.edit', 3) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="mdi mdi-square-edit-outline fs-16"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deletePermission(3)">
                                        <i class="mdi mdi-trash-can-outline fs-16"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td><span class="badge bg-light text-primary fs-12">role-manage</span></td>
                                <td>Ability to assign roles and permissions to users</td>
                                <td>web</td>
                                <td>2026-07-24 10:15:00</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.permissions.edit', 4) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="mdi mdi-square-edit-outline fs-16"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deletePermission(4)">
                                        <i class="mdi mdi-trash-can-outline fs-16"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td><span class="badge bg-light text-primary fs-12">setting-manage</span></td>
                                <td>Ability to modify system wide settings</td>
                                <td>web</td>
                                <td>2026-07-24 10:20:00</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.permissions.edit', 5) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="mdi mdi-square-edit-outline fs-16"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deletePermission(5)">
                                        <i class="mdi mdi-trash-can-outline fs-16"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-bottom')
    @vite([
        'resources/js/pages/datatable.init.js'
    ])
    
    <script>
        function deletePermission(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Deleted!',
                        'Permission has been deleted.',
                        'success'
                    )
                }
            })
        }
    </script>
@endsection