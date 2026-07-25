@extends('layouts.error', ['title' => '401 - Unauthorized Access'])

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
                    <i class="mdi mdi-account-lock-outline display-3"></i>
                </span>
            </div>

            <h2 class="fw-bold text-dark mb-2">401</h2>
            <h4 class="fw-semibold text-dark mb-3">Unauthorized Authentication</h4>
            <p class="text-muted fs-13 mb-4">You must be logged in with a valid user session to access this page.</p>

            <a class="btn btn-primary fw-bold px-4 py-2" href="{{ route('login') }}">
                <i class="mdi mdi-login me-1"></i>Log In Now
            </a>
        </div>
    </div>
</div>
@endsection
