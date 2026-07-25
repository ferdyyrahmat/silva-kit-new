@extends('layouts.vertical', ['title' => __('messages.maintenance_settings')])

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">{{ __('messages.maintenance_settings') }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('messages.maintenance') }}</li>
            </ol>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8 col-lg-10 col-12 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('messages.configure_maintenance') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.maintenance.store') }}" method="POST">
                        @csrf
                        
                        <!-- Toggle Switch -->
                        <div class="mb-4">
                            <label class="form-label d-block fw-semibold mb-2">{{ __('messages.maintenance_status') }}</label>
                            <div class="form-check form-switch form-switch-lg">
                                <input type="hidden" name="status" value="0">
                                <input class="form-check-input" type="checkbox" name="status" id="maintenanceStatus" value="1" {{ $status ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="maintenanceStatus">
                                    <span id="statusBadge" class="badge {{ $status ? 'bg-danger' : 'bg-success' }}">
                                        {{ $status ? __('messages.on_active') : __('messages.off_inactive') }}
                                    </span>
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">{{ __('messages.maintenance_notice') }}</small>
                        </div>

                        <!-- Maintenance Title -->
                        <div class="mb-3">
                            <label for="maintenanceTitle" class="form-label fw-semibold">{{ __('messages.maintenance_title') }}</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="maintenanceTitle" name="title" value="{{ old('title', $title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Maintenance Message -->
                        <div class="mb-4">
                            <label for="maintenanceMessage" class="form-label fw-semibold">{{ __('messages.maintenance_msg') }}</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="maintenanceMessage" name="message" rows="4" required>{{ old('message', $message) }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <a href="{{ route('maintenance.page') }}" target="_blank" class="btn btn-outline-info">
                                <i class="mdi mdi-eye-outline me-1"></i> {{ __('messages.preview_page') }}
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="mdi mdi-content-save-outline me-1"></i> {{ __('messages.save_settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script-bottom')
<script>
    document.getElementById('maintenanceStatus').addEventListener('change', function() {
        const badge = document.getElementById('statusBadge');
        if (this.checked) {
            badge.textContent = "{{ __('messages.on_active') }}";
            badge.className = 'badge bg-danger';
        } else {
            badge.textContent = "{{ __('messages.off_inactive') }}";
            badge.className = 'badge bg-success';
        }
    });
</script>
@endsection