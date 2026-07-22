@extends('layouts.error', ['title' => 'Error 503'])

@section('content')
<div class="col-md-5 mx-auto">
    <div class="card p-3 mb-0">
        <div class="card-body">
            <div class="text-center">
                <div class="mb-4">
                    <a href="{{ route('second', [ 'dashboard' , 'index']) }}" class="auth-logo">
                        <img src="/images/logo-dark.png" alt="logo-dark" class="mx-auto" height="28" />
                    </a>
                </div>

                <div class="maintenance-img">
                    <img src="/images/svg/503-error.svg" class="img-fluid" alt="coming-soon">
                </div>

                <div class="text-center">
                    <h3 class="mt-4 fw-semibold text-black text-capitalize">Service unavailable</h3>
                    <p class="text-muted">Temporary service outage. Please try again later.</p>
                </div>

                <a class="btn btn-primary mt-3 me-1" href="{{ route('second', [ 'dashboard' , 'index']) }}">Back to Home</a>
            </div>
        </div>
    </div>
</div>
@endsection