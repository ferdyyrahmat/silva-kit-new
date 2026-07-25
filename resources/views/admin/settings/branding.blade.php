@extends('layouts.vertical', ['title' => __('messages.app_branding')])

@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">{{ __('messages.app_branding') }}</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('root') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('messages.app_branding') }}</li>
            </ol>
        </div>
    </div>

    <!-- Form Container -->
    <form id="form-branding-settings" action="{{ route('admin.settings.branding.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- App Identity & Meta SEO -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-body-tertiary py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="mdi mdi-application-cog-outline text-primary me-1"></i>{{ __('messages.app_branding') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-13">{{ __('messages.app_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="app_name" value="{{ $settings['app_name'] }}" required placeholder="e.g. Silva Kit Enterprise">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-13">{{ __('messages.meta_description') }}</label>
                            <textarea class="form-control" name="meta_description" rows="3" placeholder="Enter site meta description...">{{ $settings['meta_description'] }}</textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark fs-13">{{ __('messages.meta_keywords') }}</label>
                                <input type="text" class="form-control" name="meta_keywords" value="{{ $settings['meta_keywords'] }}" placeholder="silva, admin, enterprise">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark fs-13">{{ __('messages.meta_author') }}</label>
                                <input type="text" class="form-control" name="meta_author" value="{{ $settings['meta_author'] }}" placeholder="e.g. Silva Team">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo & Asset Uploads -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-body-tertiary py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="mdi mdi-image-multiple-outline text-primary me-1"></i>{{ __('messages.logos_favicon') }}</h5>
                    </div>
                    <div class="card-body">
                        <!-- Light Mode Logo -->
                        <div class="mb-4 pb-3 border-bottom">
                            <label class="form-label fw-semibold text-dark fs-13 d-block">{{ __('messages.light_logo') }}</label>
                            <div class="d-flex align-items-center mb-2 p-2 bg-dark rounded border">
                                <img src="{{ $settings['app_logo_light'] }}" alt="Light Logo Preview" class="img-fluid me-2" style="max-height: 36px;" id="preview-logo-light">
                            </div>
                            <input type="file" class="form-control form-control-sm" name="app_logo_light" accept="image/*" onchange="previewImage(this, '#preview-logo-light')">
                        </div>

                        <!-- Dark Mode Logo -->
                        <div class="mb-4 pb-3 border-bottom">
                            <label class="form-label fw-semibold text-dark fs-13 d-block">{{ __('messages.dark_logo') }}</label>
                            <div class="d-flex align-items-center mb-2 p-2 bg-light rounded border">
                                <img src="{{ $settings['app_logo_dark'] }}" alt="Dark Logo Preview" class="img-fluid me-2" style="max-height: 36px;" id="preview-logo-dark">
                            </div>
                            <input type="file" class="form-control form-control-sm" name="app_logo_dark" accept="image/*" onchange="previewImage(this, '#preview-logo-dark')">
                        </div>

                        <!-- Small Sidebar Icon Logo -->
                        <div class="mb-4 pb-3 border-bottom">
                            <label class="form-label fw-semibold text-dark fs-13 d-block">{{ __('messages.small_logo') }}</label>
                            <div class="d-flex align-items-center mb-2 p-2 bg-light rounded border">
                                <img src="{{ $settings['app_logo_sm'] }}" alt="Small Logo Preview" class="img-fluid me-2" style="max-height: 32px;" id="preview-logo-sm">
                            </div>
                            <input type="file" class="form-control form-control-sm" name="app_logo_sm" accept="image/*" onchange="previewImage(this, '#preview-logo-sm')">
                        </div>

                        <!-- App Favicon -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-13 d-block">{{ __('messages.favicon') }}</label>
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ $settings['app_favicon'] }}" alt="Favicon Preview" width="32" height="32" class="me-2 rounded" id="preview-favicon">
                            </div>
                            <input type="file" class="form-control form-control-sm" name="app_favicon" accept="image/*,.ico" onchange="previewImage(this, '#preview-favicon')">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="col-12 text-end mb-4">
                <button type="submit" class="btn btn-primary fw-bold px-4 py-2" id="btn-save-branding">
                    <i class="mdi mdi-content-save-outline me-1"></i>{{ __('messages.save_settings') }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script-bottom')
<script>
    function previewImage(input, selector) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(selector).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        $('#form-branding-settings').on('submit', function(e) {
            e.preventDefault();
            var btn = $('#btn-save-branding');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline me-1"></i>{{ __("messages.save_settings") }}');
                    if (res.success) {
                        Swal.fire('Success!', res.message, 'success').then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline me-1"></i>{{ __("messages.save_settings") }}');
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to update branding settings.';
                    Swal.fire('Error!', msg, 'error');
                }
            });
        });
    });
</script>
@endsection
