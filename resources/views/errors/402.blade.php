@extends('layouts.error', ['title' => '402 - Payment Required'])

@section('content')
<div class="col-md-5 col-lg-4 mx-auto py-5">
    <div class="card border-0 shadow-lg p-4 rounded-4 text-center">
        <div class="card-body">
            <div class="mb-3">
                <a href="{{ route('root') }}" class="auth-logo">
                    <img src="/images/logo-dark.png" alt="logo-dark" class="mx-auto" height="32"/>
                </a>
            </div>

            <div class="my-4">
                <span class="avatar-title bg-info-subtle text-info rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                    <i class="mdi mdi-credit-card-outline display-3"></i>
                </span>
            </div>

            <h2 class="fw-bold text-dark mb-2">402</h2>
            <h4 class="fw-semibold text-dark mb-3">Payment Required</h4>
            <p class="text-muted fs-13 mb-4">Access to this feature or service requires an active subscription or completed payment.</p>

            <a class="btn btn-primary fw-bold px-4 py-2" href="{{ route('root') }}">
                <i class="mdi mdi-home me-1"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
