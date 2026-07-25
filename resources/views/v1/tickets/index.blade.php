@extends('layouts.vertical', ['title' => 'My Support Tickets'])

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">My Support Tickets</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('v1.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Support Tickets</li>
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

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold text-body"><i class="mdi mdi-ticket-confirmation-outline text-primary me-1"></i>Submitted Support Requests</h5>
                    <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#global-feedback-modal">
                        <i class="mdi mdi-plus-circle-outline me-1"></i>Create New Ticket
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 fs-13">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Ticket Code</th>
                                    <th>Subject / Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $t)
                                    <tr>
                                        <td class="ps-3">
                                            <a href="{{ route('v1.tickets.show', $t->ticket_code) }}" class="fw-bold font-monospace text-primary fs-14">
                                                #{{ $t->ticket_code }}
                                            </a>
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
                                            <span class="badge bg-{{ match($t->priority) { 'urgent' => 'danger', 'high' => 'warning', default => 'info' } }} text-white text-uppercase">
                                                {{ $t->priority }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $stBadge = match($t->status) {
                                                    'resolved' => 'success',
                                                    'in_progress' => 'primary',
                                                    'waiting_user' => 'info',
                                                    'closed' => 'secondary',
                                                    default => 'warning'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $stBadge }}-subtle text-{{ $stBadge }} fw-bold text-uppercase">
                                                <i class="mdi {{ $t->status == 'resolved' ? 'mdi-check-circle' : ($t->status == 'in_progress' ? 'mdi-clock-outline' : 'mdi-alert-circle-outline') }} me-1"></i>{{ str_replace('_', ' ', $t->status) }}
                                            </span>
                                        </td>
                                        <td class="text-muted fs-12">{{ $t->updated_at->diffForHumans() }}</td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('v1.tickets.show', $t->ticket_code) }}" class="btn btn-outline-primary btn-xs">
                                                <i class="mdi mdi-forum-outline me-1"></i>Track & Reply
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-ticket-outline fs-36 text-muted d-block mb-2"></i>
                                            <p class="mb-0 fw-semibold text-dark">You have not submitted any support tickets yet.</p>
                                            <small>Click "Create New Ticket" or use the floating button to submit a ticket.</small>
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
