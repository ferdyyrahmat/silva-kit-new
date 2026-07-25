@extends('layouts.vertical', ['title' => __('messages.queues_redis')])

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">{{ __('messages.queues_redis') }}</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('messages.queues_redis') }}</li>
            </ol>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 text-uppercase fw-bold">{{ __('messages.queue_worker_status') }}</span>
                            <h4 class="fw-bold mb-0 text-primary mt-1 font-monospace">{{ strtoupper($queueConnection) }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-20">
                                <i class="mdi mdi-tray-full"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 text-uppercase fw-bold">{{ __('messages.redis_status') }}</span>
                            <h4 class="fw-bold mb-0 mt-1 {{ $redisStatus == 'Connected' ? 'text-success' : 'text-warning' }}">
                                <i class="mdi {{ $redisStatus == 'Connected' ? 'mdi-check-circle' : 'mdi-alert-circle' }} me-1"></i>{{ $redisStatus }}
                            </h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-20">
                                <i class="mdi mdi-server-network"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 text-uppercase fw-bold">{{ __('messages.total_failed_jobs') }}</span>
                            <h4 class="fw-bold mb-0 {{ count($failedJobs) > 0 ? 'text-danger' : 'text-muted' }} mt-1">{{ count($failedJobs) }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-20">
                                <i class="mdi mdi-alert-octagram-outline"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Jobs Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fs-15 fw-bold text-body"><i class="mdi mdi-history text-danger me-1"></i>{{ __('messages.failed_jobs') }}</h5>
                    @if(count($failedJobs) > 0)
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="purgeAllFailedJobs()">
                            <i class="mdi mdi-delete-sweep-outline me-1"></i>{{ __('messages.flush_failed') }}
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0 fs-13">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">ID</th>
                                    <th>Connection / Queue</th>
                                    <th>Payload / Job Class</th>
                                    <th>{{ __('messages.timestamp') }}</th>
                                    <th class="text-end pe-3">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($failedJobs as $job)
                                    @php
                                        $payload = json_decode($job->payload, true);
                                        $displayName = $payload['displayName'] ?? 'Unknown Job';
                                    @endphp
                                    <tr id="job-row-{{ $job->id }}">
                                        <td class="ps-3 font-monospace fw-bold">#{{ $job->id }}</td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-dark font-monospace">{{ $job->connection }}</span>
                                            <span class="badge bg-light text-muted font-monospace ms-1">{{ $job->queue }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-body d-block">{{ $displayName }}</span>
                                            <small class="text-danger text-truncate d-inline-block style-max-width">{{ $job->exception }}</small>
                                        </td>
                                        <td class="text-muted">{{ $job->failed_at }}</td>
                                        <td class="text-end pe-3">
                                            <button type="button" class="btn btn-outline-primary btn-xs me-1" onclick="retryJob('{{ $job->id }}')">
                                                <i class="mdi mdi-refresh me-1"></i>{{ __('messages.retry_job') }}
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-xs" onclick="deleteJob('{{ $job->id }}')">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-check-circle-outline fs-36 text-success d-block mb-2"></i>
                                            <p class="mb-0 fs-14 fw-semibold text-dark">{{ __('messages.no_data') }}</p>
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
    function retryJob(id) {
        Swal.fire({
            title: '{{ __("messages.retry_job") }} #' + id + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '{{ __("messages.retry_job") }}',
            cancelButtonText: '{{ __("messages.cancel") }}'
        }).then((res) => {
            if (res.isConfirmed) {
                $.ajax({
                    url: '/admin/queues/' + id + '/retry',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(resp) {
                        $('#job-row-' + id).fadeOut(300, function() { $(this).remove(); });
                        Swal.fire('Success', resp.message, 'success');
                    }
                });
            }
        });
    }

    function deleteJob(id) {
        Swal.fire({
            title: '{{ __("messages.confirm_delete") }}',
            text: 'Job #' + id,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: '{{ __("messages.yes_delete") }}',
            cancelButtonText: '{{ __("messages.cancel") }}'
        }).then((res) => {
            if (res.isConfirmed) {
                $.ajax({
                    url: '/admin/queues/' + id,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(resp) {
                        $('#job-row-' + id).fadeOut(300, function() { $(this).remove(); });
                        Swal.fire('Deleted', resp.message, 'success');
                    }
                });
            }
        });
    }

    function purgeAllFailedJobs() {
        Swal.fire({
            title: '{{ __("messages.flush_failed") }}?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: '{{ __("messages.yes_delete") }}',
            cancelButtonText: '{{ __("messages.cancel") }}'
        }).then((res) => {
            if (res.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.queues.purge") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(resp) {
                        Swal.fire('Flushed!', resp.message, 'success').then(() => window.location.reload());
                    }
                });
            }
        });
    }
</script>
@endsection
