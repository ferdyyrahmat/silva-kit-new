@extends('layouts.vertical', ['title' => 'Support Ticket Center'])

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Support Ticket Center</h4>
        </div>
        <div class="text-end">
            <a href="{{ route('admin.tickets.developers.index') }}" class="btn btn-outline-primary btn-sm me-2 fw-bold">
                <i class="mdi mdi-account-code-outline me-1"></i>Developer Team Settings
            </a>
            <ol class="breadcrumb m-0 py-0 d-inline-flex align-items-center">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Tickets</li>
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

    <!-- Ticket Metric Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 text-uppercase fw-bold">Total Tickets</span>
                            <h4 class="fw-bold mb-0 text-primary mt-1">{{ $stats['total'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-20">
                                <i class="mdi mdi-ticket-account"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 text-uppercase fw-bold">Open Tickets</span>
                            <h4 class="fw-bold mb-0 text-danger mt-1">{{ $stats['open'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-20">
                                <i class="mdi mdi-alert-circle-outline"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 text-uppercase fw-bold">In Progress</span>
                            <h4 class="fw-bold mb-0 text-warning mt-1">{{ $stats['in_progress'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-20">
                                <i class="mdi mdi-clock-outline"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 text-uppercase fw-bold">Resolved</span>
                            <h4 class="fw-bold mb-0 text-success mt-1">{{ $stats['resolved'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-20">
                                <i class="mdi mdi-check-circle-outline"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold text-body"><i class="mdi mdi-ticket-confirmation-outline text-primary me-1"></i>Support Ticket Queue</h5>
                    <div class="d-flex gap-2">
                        <form method="GET" action="{{ route('admin.tickets.index') }}" class="d-flex gap-2">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">-- All Status --</option>
                                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>🟢 Open</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>🟡 In Progress</option>
                                <option value="waiting_user" {{ request('status') === 'waiting_user' ? 'selected' : '' }}>⏳ Waiting User</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>🔵 Resolved</option>
                                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>⚫ Closed</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 fs-13">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Ticket Code</th>
                                    <th>Submitter</th>
                                    <th>Subject / Category</th>
                                    <th>Priority</th>
                                    <th>Assigned Dev</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $t)
                                    <tr>
                                        <td class="ps-3">
                                            <a href="{{ route('admin.tickets.show', $t->id) }}" class="fw-bold font-monospace text-primary">
                                                #{{ $t->ticket_code }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $t->name }}</div>
                                            <small class="text-muted">{{ $t->email }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $catClass = match($t->category) {
                                                    'bug' => 'danger',
                                                    'server_issue' => 'warning',
                                                    'feature_request' => 'info',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $catClass }}-subtle text-{{ $catClass }} font-monospace text-uppercase me-1">
                                                {{ str_replace('_', ' ', $t->category) }}
                                            </span>
                                            <span class="fw-semibold text-dark">{{ $t->subject }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $prioBadge = match($t->priority) {
                                                    'urgent' => 'danger',
                                                    'high' => 'warning',
                                                    'medium' => 'info',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $prioBadge }} text-white text-uppercase fs-11">
                                                {{ $t->priority }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($t->assignedDeveloper)
                                                <span class="badge bg-info-subtle text-info fw-semibold">
                                                    <i class="mdi mdi-account-code me-1"></i>{{ $t->assignedDeveloper->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-muted fs-11">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $stBadge = match($t->status) {
                                                    'resolved' => 'success',
                                                    'in_progress' => 'primary',
                                                    'waiting_user' => 'info',
                                                    'closed' => 'secondary',
                                                    default => 'danger'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $stBadge }}-subtle text-{{ $stBadge }} fw-bold text-uppercase">
                                                {{ str_replace('_', ' ', $t->status) }}
                                            </span>
                                        </td>
                                        <td class="text-muted fs-12">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('admin.tickets.show', $t->id) }}" class="btn btn-outline-primary btn-xs me-1">
                                                <i class="mdi mdi-eye-outline me-1"></i>View & Reply
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-xs" onclick="deleteTicket({{ $t->id }}, '{{ $t->ticket_code }}')">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-ticket-outline fs-36 text-muted d-block mb-2"></i>
                                            <p class="mb-0 fw-semibold text-dark">No support tickets found.</p>
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
    function deleteTicket(id, code) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Ticket #' + code + '?',
                text: 'This ticket and all its replies will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, Delete'
            }).then((res) => {
                if (res.isConfirmed) {
                    $.ajax({
                        url: '/admin/tickets/' + id,
                        type: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        success: function(resp) {
                            Swal.fire('Deleted!', resp.message, 'success').then(() => {
                                window.location.href = resp.redirect || window.location.href;
                            });
                        }
                    });
                }
            });
        }
    }
</script>
@endsection
