@extends('layouts.vertical', ['title' => 'Notification Blast & Multi-Channel Connectors'])

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Notification Blast & Multi-Channel Engine</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Notification Blast</li>
            </ol>
        </div>
    </div>

    <!-- Alert Notifications -->
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

    <!-- Main Navigation Tabs -->
    <ul class="nav nav-tabs nav-bordered mb-3" role="tablist">
        <li class="nav-item">
            <a href="#tab-broadcast" data-bs-toggle="tab" aria-expanded="true" class="nav-item-tab nav-link active py-2">
                <i class="mdi mdi-bullhorn-outline fs-18 me-1 align-middle"></i>
                <span class="fw-semibold fs-14">Broadcast Blast</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#tab-connectors" data-bs-toggle="tab" aria-expanded="false" class="nav-item-tab nav-link py-2">
                <i class="mdi mdi-api fs-18 me-1 align-middle"></i>
                <span class="fw-semibold fs-14">Connector Settings & APIs</span>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- TAB 1: BROADCAST BLAST -->
        <div class="tab-pane show active" id="tab-broadcast">
            <div class="row">
                <!-- Left: Create Blast Form -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-primary-subtle py-3">
                            <h5 class="card-title mb-0 text-primary fw-bold">
                                <i class="mdi mdi-send-outline me-1"></i>New Notification Blast
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="form-send-blast" action="{{ route('admin.notifications.send-blast') }}" method="POST">
                                @csrf
                                <input type="hidden" name="target_id" id="hidden-target-id">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Title / Subject</label>
                                    <input type="text" class="form-control" name="title" required placeholder="e.g. System Maintenance Notice">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Notification Type</label>
                                    <div class="d-flex gap-2">
                                        <label class="btn btn-outline-info btn-sm flex-fill active">
                                            <input type="radio" name="type" value="info" checked class="d-none"> <i class="mdi mdi-information-outline me-1"></i>Info
                                        </label>
                                        <label class="btn btn-outline-success btn-sm flex-fill">
                                            <input type="radio" name="type" value="success" class="d-none"> <i class="mdi mdi-check-circle-outline me-1"></i>Success
                                        </label>
                                        <label class="btn btn-outline-warning btn-sm flex-fill">
                                            <input type="radio" name="type" value="warning" class="d-none"> <i class="mdi mdi-alert-outline me-1"></i>Warning
                                        </label>
                                        <label class="btn btn-outline-danger btn-sm flex-fill">
                                            <input type="radio" name="type" value="danger" class="d-none"> <i class="mdi mdi-alert-circle-outline me-1"></i>Danger
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Target Audience</label>
                                    <select class="form-select mb-2" name="target_type" id="select-target-type">
                                        <option value="all">🌐 All Users in System</option>
                                        <option value="role">👥 Specific Role Group</option>
                                        <option value="user">👤 Specific Individual User</option>
                                    </select>

                                    <div id="target-role-container" class="d-none mb-2">
                                        <select class="form-select" id="select-target-role">
                                            <option value="">-- Select Role Group --</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="target-user-container" class="d-none mb-2">
                                        <select class="form-select" id="select-target-user">
                                            <option value="">-- Select Specific User --</option>
                                            @foreach($users as $u)
                                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Multi-Channel Selection Checkboxes -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Notification Channels (Connectors)</label>
                                    <div class="card bg-body-tertiary border p-3 rounded-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="channels[]" value="bell" id="chk-bell" checked>
                                            <label class="form-check-label fw-semibold" for="chk-bell">
                                                <i class="mdi mdi-bell-outline text-primary me-1"></i>In-App Bell & System Inbox
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="channels[]" value="email" id="chk-email" {{ $connectorSettings['email']['enabled'] ? '' : 'disabled' }}>
                                            <label class="form-check-label fw-semibold {{ $connectorSettings['email']['enabled'] ? '' : 'text-muted' }}" for="chk-email">
                                                <i class="mdi mdi-email-outline text-info me-1"></i>Email (SMTP Channel)
                                                @if(!$connectorSettings['email']['enabled'])
                                                    <span class="badge bg-secondary-subtle text-muted ms-1">Disabled</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp" id="chk-whatsapp" {{ $connectorSettings['whatsapp']['enabled'] ? '' : 'disabled' }}>
                                            <label class="form-check-label fw-semibold {{ $connectorSettings['whatsapp']['enabled'] ? '' : 'text-muted' }}" for="chk-whatsapp">
                                                <i class="mdi mdi-whatsapp text-success me-1"></i>WhatsApp Gateway API
                                                @if(!$connectorSettings['whatsapp']['enabled'])
                                                    <span class="badge bg-secondary-subtle text-muted ms-1">Disabled</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="channels[]" value="telegram" id="chk-telegram" {{ $connectorSettings['telegram']['enabled'] ? '' : 'disabled' }}>
                                            <label class="form-check-label fw-semibold {{ $connectorSettings['telegram']['enabled'] ? '' : 'text-muted' }}" for="chk-telegram">
                                                <i class="mdi mdi-telegram text-primary me-1"></i>Telegram Bot / Channel
                                                @if(!$connectorSettings['telegram']['enabled'])
                                                    <span class="badge bg-secondary-subtle text-muted ms-1">Disabled</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Message Body</label>
                                    <textarea class="form-control" name="message" rows="4" required placeholder="Write your broadcast message content here..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Action URL (Optional)</label>
                                    <input type="url" class="form-control" name="url" placeholder="https://example.com/target-page">
                                </div>

                                <button type="submit" class="btn btn-primary w-100 fw-bold py-2" id="btn-dispatch-blast">
                                    <i class="mdi mdi-send me-1"></i>Dispatch Notification Blast
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right: Blast History Table -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-body-tertiary py-3">
                            <h5 class="card-title mb-0 fw-bold text-body"><i class="mdi mdi-history me-1 text-primary"></i>Broadcast History Log</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 fs-13">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Blast Details</th>
                                            <th>Target Audience</th>
                                            <th>Channels Used</th>
                                            <th>Sent / Failed</th>
                                            <th>Date Sent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($blasts as $b)
                                            <tr>
                                                <td class="ps-3">
                                                    <span class="badge bg-{{ $b->type }}-subtle text-{{ $b->type }} mb-1 font-monospace text-uppercase">{{ $b->type }}</span>
                                                    <h6 class="fw-bold text-dark mb-0 fs-14">{{ $b->title }}</h6>
                                                    <small class="text-muted text-truncate d-inline-block style-max-width">{{ $b->message }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary font-monospace">{{ strtoupper($b->target_type) }}</span>
                                                </td>
                                                <td>
                                                    @foreach($b->channels as $ch)
                                                        @if($ch == 'bell') <span class="badge bg-primary-subtle text-primary me-1"><i class="mdi mdi-bell-outline"></i> Bell</span> @endif
                                                        @if($ch == 'email') <span class="badge bg-info-subtle text-info me-1"><i class="mdi mdi-email-outline"></i> Email</span> @endif
                                                        @if($ch == 'whatsapp') <span class="badge bg-success-subtle text-success me-1"><i class="mdi mdi-whatsapp"></i> WA</span> @endif
                                                        @if($ch == 'telegram') <span class="badge bg-secondary-subtle text-dark me-1"><i class="mdi mdi-telegram"></i> Telegram</span> @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <span class="text-success fw-bold me-1"><i class="mdi mdi-check-circle-outline"></i> {{ $b->sent_count }}</span>
                                                    @if($b->failed_count > 0)
                                                        <span class="text-danger fw-bold"><i class="mdi mdi-alert-circle-outline"></i> {{ $b->failed_count }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted fs-12">{{ $b->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="mdi mdi-bullhorn-outline fs-36 text-muted d-block mb-2"></i>
                                                    <p class="mb-0 fw-semibold text-dark">No notification blasts sent yet.</p>
                                                    <small>Use the form on the left to dispatch your first multi-channel broadcast.</small>
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

        <!-- TAB 2: CONNECTOR SETTINGS & APIS -->
        <div class="tab-pane" id="tab-connectors">
            <form id="form-connector-settings" action="{{ route('admin.notifications.settings.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <!-- SMTP Email Connector Card -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-info-subtle d-flex justify-content-between align-items-center py-3">
                                <h5 class="card-title mb-0 text-info fw-bold"><i class="mdi mdi-email-outline me-1"></i>SMTP Email Channel</h5>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="email_enabled" value="1" {{ $connectorSettings['email']['enabled'] ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">SMTP Host</label>
                                    <input type="text" class="form-control" name="email_host" value="{{ $connectorSettings['email']['host'] }}" placeholder="smtp.mailtrap.io">
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label fw-semibold">Port</label>
                                        <input type="text" class="form-control" name="email_port" value="{{ $connectorSettings['email']['port'] }}" placeholder="587">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label fw-semibold">Encryption</label>
                                        <select class="form-select" name="email_encryption">
                                            <option value="tls" {{ $connectorSettings['email']['encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ $connectorSettings['email']['encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="none" {{ $connectorSettings['email']['encryption'] == 'none' ? 'selected' : '' }}>None</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Username</label>
                                    <input type="text" class="form-control" name="email_username" value="{{ $connectorSettings['email']['username'] }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Password</label>
                                    <input type="password" class="form-control" name="email_password" placeholder="••••••••">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">From Sender Address</label>
                                    <input type="email" class="form-control" name="email_from_address" value="{{ $connectorSettings['email']['from_address'] }}">
                                </div>
                            </div>
                            <div class="card-footer bg-light p-3">
                                <button type="button" class="btn btn-outline-info btn-sm w-100 fw-bold" onclick="testConnector('email')">
                                    <i class="mdi mdi-send-check me-1"></i>Test SMTP Email Connection
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Gateway Connector Card -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-success-subtle d-flex justify-content-between align-items-center py-3">
                                <h5 class="card-title mb-0 text-success fw-bold"><i class="mdi mdi-whatsapp me-1"></i>WhatsApp Gateway API</h5>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="whatsapp_enabled" value="1" {{ $connectorSettings['whatsapp']['enabled'] ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Gateway Provider</label>
                                    <select class="form-select" name="whatsapp_provider">
                                        <option value="fonnte" {{ $connectorSettings['whatsapp']['provider'] == 'fonnte' ? 'selected' : '' }}>Fonnte API Gateway</option>
                                        <option value="wablas" {{ $connectorSettings['whatsapp']['provider'] == 'wablas' ? 'selected' : '' }}>Wablas API Gateway</option>
                                        <option value="generic" {{ $connectorSettings['whatsapp']['provider'] == 'generic' ? 'selected' : '' }}>Generic Webhook POST</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">API Endpoint URL</label>
                                    <input type="text" class="form-control" name="whatsapp_api_url" value="{{ $connectorSettings['whatsapp']['api_url'] }}" placeholder="https://api.fonnte.com/send">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">API Token / Secret Key</label>
                                    <input type="password" class="form-control" name="whatsapp_token" value="{{ $connectorSettings['whatsapp']['token'] }}" placeholder="API Token Key">
                                </div>
                            </div>
                            <div class="card-footer bg-light p-3">
                                <button type="button" class="btn btn-outline-success btn-sm w-100 fw-bold" onclick="testConnector('whatsapp')">
                                    <i class="mdi mdi-send-check me-1"></i>Test WhatsApp API Connection
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Telegram Bot Connector Card -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-primary-subtle d-flex justify-content-between align-items-center py-3">
                                <h5 class="card-title mb-0 text-primary fw-bold"><i class="mdi mdi-telegram me-1"></i>Telegram Bot Channel</h5>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="telegram_enabled" value="1" {{ $connectorSettings['telegram']['enabled'] ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Telegram Bot Token</label>
                                    <input type="password" class="form-control" name="telegram_bot_token" value="{{ $connectorSettings['telegram']['bot_token'] }}" placeholder="123456789:ABCdefGhIJKlmNoPQ">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Default Chat ID / Channel ID</label>
                                    <input type="text" class="form-control" name="telegram_chat_id" value="{{ $connectorSettings['telegram']['chat_id'] }}" placeholder="-1001234567890 or @channel">
                                </div>
                            </div>
                            <div class="card-footer bg-light p-3">
                                <button type="button" class="btn btn-outline-primary btn-sm w-100 fw-bold" onclick="testConnector('telegram')">
                                    <i class="mdi mdi-send-check me-1"></i>Test Telegram Bot Connection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                        <i class="mdi mdi-content-save-outline me-1"></i>Save All Connector Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script-bottom')
<script>
    $(document).ready(function() {
        // Function to update hidden target ID based on selection
        function syncTargetId() {
            var val = $('#select-target-type').val();
            if (val === 'role') {
                $('#target-role-container').removeClass('d-none');
                $('#target-user-container').addClass('d-none');
                $('#hidden-target-id').val($('#select-target-role').val());
            } else if (val === 'user') {
                $('#target-user-container').removeClass('d-none');
                $('#target-role-container').addClass('d-none');
                $('#hidden-target-id').val($('#select-target-user').val());
            } else {
                $('#target-role-container').addClass('d-none');
                $('#target-user-container').addClass('d-none');
                $('#hidden-target-id').val('');
            }
        }

        // Target Audience Dynamic Selector listeners
        $('#select-target-type').on('change', syncTargetId);
        $('#select-target-role').on('change', function() {
            if ($('#select-target-type').val() === 'role') {
                $('#hidden-target-id').val($(this).val());
            }
        });
        $('#select-target-user').on('change', function() {
            if ($('#select-target-type').val() === 'user') {
                $('#hidden-target-id').val($(this).val());
            }
        });
    });

    // Test Connector Ajax Handler (For live connection testing buttons)
    function testConnector(channel) {
        var token = $('input[name="' + channel + '_token"]').val() || $('input[name="' + channel + '_bot_token"]').val();
        var apiUrl = $('input[name="' + channel + '_api_url"]').val() || $('input[name="' + channel + '_host"]').val();
        var provider = $('select[name="' + channel + '_provider"]').val();

        Swal.fire({
            title: 'Test ' + channel.toUpperCase() + ' Connection',
            text: 'Enter test recipient (email, phone, or chat ID):',
            input: 'text',
            inputValue: channel === 'email' ? '{{ Auth::user()->email }}' : (channel === 'whatsapp' ? '6289524424936' : ''),
            inputPlaceholder: channel === 'email' ? 'your-email@domain.com' : (channel === 'whatsapp' ? '6289524424936' : 'Chat ID'),
            showCancelButton: true,
            confirmButtonText: 'Send Test Message',
            showLoaderOnConfirm: true,
            preConfirm: (recipient) => {
                return $.ajax({
                    url: '{{ route("admin.notifications.test-connector") }}',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: {
                        channel: channel,
                        recipient: recipient,
                        token: token,
                        api_url: apiUrl,
                        provider: provider
                    }
                }).catch(error => {
                    Swal.showValidationMessage(`Test failed: ${error.responseJSON ? error.responseJSON.message : 'Connection error'}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                Swal.fire('Connection Successful!', result.value.message, 'success');
            }
        });
    }
</script>
@endsection