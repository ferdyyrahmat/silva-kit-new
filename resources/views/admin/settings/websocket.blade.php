@extends('layouts.vertical', ['title' => 'WebSocket & Pusher Configuration'])

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">WebSocket & Pusher Configuration</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">WebSocket Settings</li>
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
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h5 class="card-title mb-1 text-body fw-bold">
                            <i class="mdi mdi-access-point-network text-primary me-1"></i>Pusher & Realtime WebSockets
                        </h5>
                        <p class="text-muted fs-13 mb-0">Configure your Pusher API keys to enable instant real-time live chat and notifications across the entire project.</p>
                    </div>
                    <div>
                        @if($settings['enabled'])
                            <span class="badge bg-success-subtle text-success fw-bold text-uppercase px-3 py-2 fs-12">
                                <i class="mdi mdi-check-circle me-1"></i>WebSocket Active
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-muted fw-bold text-uppercase px-3 py-2 fs-12">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.settings.websocket.update') }}" method="POST">
                        @csrf
                        
                        <div class="form-check form-switch mb-4 p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-check-label fw-bold text-dark fs-14 me-2" for="websocket_enabled">
                                    Enable Real-time WebSockets & Pusher
                                </label>
                                <div class="text-muted fs-12">When enabled, ticket chat replies, status updates, and notifications will be pushed live in 0.00s.</div>
                            </div>
                            <input class="form-check-input fs-20 me-2" type="checkbox" role="switch" name="websocket_enabled" value="1" id="websocket_enabled" {{ $settings['enabled'] ? 'checked' : '' }}>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13">Pusher App ID</label>
                                <input type="text" class="form-control" name="pusher_app_id" value="{{ $settings['app_id'] }}" placeholder="e.g. 1234567">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13">Pusher App Key (Public)</label>
                                <input type="text" class="form-control" name="pusher_app_key" value="{{ $settings['app_key'] }}" placeholder="e.g. a1b2c3d4e5f6">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13">Pusher App Secret</label>
                                <input type="password" class="form-control" name="pusher_app_secret" value="{{ $settings['app_secret'] }}" placeholder="••••••••••••••••">
                                <small class="text-muted fs-11">Leave blank to keep existing secret.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-13">Pusher App Cluster</label>
                                <input type="text" class="form-control" name="pusher_app_cluster" value="{{ $settings['app_cluster'] }}" placeholder="e.g. ap1, us2, eu">
                            </div>

                            <hr class="my-3 text-muted opacity-25">

                            <h6 class="fw-bold text-dark mb-0 fs-13">Self-Hosted WebSockets / Soketi / Laravel Reverb (Optional)</h6>
                            <p class="text-muted fs-12 mb-2">If using Pusher Cloud, leave Host and Port blank.</p>

                            <div class="col-md-8">
                                <label class="form-label fw-semibold fs-13">Custom Hostname</label>
                                <input type="text" class="form-control" name="pusher_host" value="{{ $settings['host'] }}" placeholder="e.g. 127.0.0.1 or ws.mydomain.com">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-13">Port</label>
                                <input type="text" class="form-control" name="pusher_port" value="{{ $settings['port'] }}" placeholder="443 or 8080">
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                <i class="mdi mdi-content-save-outline me-1"></i>Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Live Connection Test Panel -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-body-tertiary py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="mdi mdi-lightning-bolt text-primary me-1"></i>Test Live Connection</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted fs-13">Click the button below to send a live broadcast event to Pusher. If configured correctly, your browser will receive the event instantly!</p>

                    <form action="{{ route('admin.settings.websocket.test') }}" method="POST" id="form-test-pusher">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100 fw-bold py-2 mb-3">
                            <i class="mdi mdi-send-check me-1"></i>Broadcast Test Event
                        </button>
                    </form>

                    <div id="test-log-container" class="bg-dark text-success p-3 rounded-3 font-monospace fs-12" style="min-height: 120px; max-height: 200px; overflow-y: auto;">
                        <div class="text-muted">Listening for live WebSocket events...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-bottom')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.PUSHER_CONFIG && window.PUSHER_CONFIG.enabled && window.PUSHER_CONFIG.key) {
            try {
                var pusher = new Pusher(window.PUSHER_CONFIG.key, {
                    cluster: window.PUSHER_CONFIG.cluster || 'ap1',
                    forceTLS: true
                });

                var channel = pusher.subscribe('silva-test-channel');
                channel.bind('test-event', function(data) {
                    var container = document.getElementById('test-log-container');
                    if (container) {
                        var log = document.createElement('div');
                        log.className = 'text-warning mb-1';
                        log.innerHTML = '⚡ [' + (data.timestamp || new Date().toLocaleTimeString()) + '] RECEIVED: ' + (data.message || 'Test broadcast received!');
                        container.appendChild(log);
                        container.scrollTop = container.scrollHeight;
                    }
                });
            } catch (e) {
                console.error("Pusher Init Error:", e);
            }
        }
    });
</script>
@endsection
