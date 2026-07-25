@extends('layouts.vertical', ['title' => __('messages.audit_trail')])

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
            <h4 class="fs-18 fw-semibold m-0">{{ __('messages.audit_trail') }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('messages.audit_trail') }}</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="mdi mdi-history me-1"></i>{{ __('messages.activity_logs') }}</h5>
                </div>

                <div class="card-body">
                    <table id="audit-datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('messages.user') }}</th>
                                <th>{{ __('messages.event') }}</th>
                                <th>{{ __('messages.description') }}</th>
                                <th>{{ __('messages.module') }}</th>
                                <th>{{ __('messages.ip_address') }}</th>
                                <th>{{ __('messages.timestamp') }}</th>
                            </tr>
                        </thead>
                        <tbody>
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
        $(document).ready(function() {
            $('#audit-datatable').DataTable({
                processing: true,
                serverSide: true,
                order: [[6, 'desc']],
                ajax: {
                    url: "{{ route('admin.audit-logs.index') }}",
                    type: "GET"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'user_name', name: 'user_name' },
                    { data: 'event', name: 'event' },
                    { data: 'action_description', name: 'action_description' },
                    { data: 'module', name: 'module' },
                    { data: 'ip_address', name: 'ip_address' },
                    { data: 'created_at', name: 'created_at' }
                ],
                drawCallback: function() {
                    $("#audit-datatable_length select").addClass('form-select form-select-sm');
                    $(".dataTables_length label").addClass('form-label');
                }
            });
        });
    </script>
@endsection
