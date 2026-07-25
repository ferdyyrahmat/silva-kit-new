@extends('layouts.vertical', ['title' => __('messages.backups')])

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">{{ __('messages.backups') }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('messages.backups') }}</li>
            </ol>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center bg-body-tertiary py-3 border-bottom">
                    <div>
                        <h5 class="card-title mb-1 text-body fw-bold"><i class="mdi mdi-zip-box-outline text-primary me-1"></i>{{ __('messages.automated_backups') }}</h5>
                        <p class="text-muted fs-13 mb-0">{{ __('messages.backup_desc') }}</p>
                    </div>
                    @if(auth()->user()->hasPermission('admin.backups.create'))
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="btn-create-backup-dropdown">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> {{ __('messages.manual_backup') }} <i class="mdi mdi-chevron-down ms-1"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="createBackup('db')"><i class="mdi mdi-database-outline me-2 text-primary"></i>{{ __('messages.db_dump') }}</a></li>
                                <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="createBackup('full')"><i class="mdi mdi-folder-zip-outline me-2 text-success"></i>{{ __('messages.full_backup') }}</a></li>
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>{{ __('messages.file_name') }}</th>
                                    <th>{{ __('messages.file_size') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                    <th class="text-end pe-3">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backupFiles as $index => $file)
                                    <tr id="backup-row-{{ Str::slug($file['name']) }}">
                                        <td class="ps-3 fw-semibold text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="mdi mdi-file-archive-outline text-danger fs-24 me-2"></i>
                                                <span class="fw-semibold text-body">{{ $file['name'] }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-secondary-subtle text-dark fs-12">{{ $file['size_mb'] }} MB</span></td>
                                        <td><span class="text-muted fs-13"><i class="mdi mdi-clock-outline me-1"></i>{{ $file['last_modified'] }}</span></td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('admin.backups.download', ['file' => $file['name']]) }}" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="mdi mdi-download me-1"></i> {{ __('messages.download') }}
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteBackup('{{ $file['name'] }}')">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-folder-zip-outline fs-36 text-muted"></i>
                                            <p class="mb-0 fs-14 mt-2">{{ __('messages.no_backups') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-bottom')
<script>
    function createBackup(type) {
        var btn = $('#btn-create-backup-dropdown');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Generating Archive...');

        $.ajax({
            url: "{{ route('admin.backups.create') }}",
            type: "POST",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { type: type },
            success: function(res) {
                btn.prop('disabled', false).html('<i class="mdi mdi-plus-circle-outline me-1"></i> {{ __("messages.manual_backup") }} <i class="mdi mdi-chevron-down ms-1"></i>');
                if (res.success) {
                    Swal.fire('Success!', res.message, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="mdi mdi-plus-circle-outline me-1"></i> {{ __("messages.manual_backup") }} <i class="mdi mdi-chevron-down ms-1"></i>');
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to generate backup.';
                Swal.fire('Backup Error', msg, 'error');
            }
        });
    }

    function deleteBackup(filename) {
        Swal.fire({
            title: '{{ __("messages.confirm_delete") }}',
            text: filename,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: '{{ __("messages.yes_delete") }}',
            cancelButtonText: '{{ __("messages.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/backups/' + encodeURIComponent(filename),
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Deleted!', res.message, 'success').then(() => window.location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Failed to delete backup file.', 'error');
                    }
                });
            }
        });
    }
</script>
@endsection
