@extends('layouts.maintenance', ['title' => 'Maintenance'])

@section('content')
<div class="col-md-5 mx-auto">
    <div class="card p-3 mb-0">
        <div class="card-body">
            <div class="text-center">
                <div class="mb-4">
                    <a class="auth-logo" href="#">
                        <img src="/images/logo-dark.png" alt="logo-dark" class="mx-auto" height="28" />
                    </a>
                </div>

                <div class="coming-soon-img text-center">
                    <img src="/images/svg/maintenance-1.svg" class="img-fluid" alt="maintenance-image">
                </div>

                <div class="text-center mt-4">
                    <h3 class="mt-0 fw-semibold text-dark text-capitalize fs-26">
                        {{ \App\Models\SystemSetting::getByKey('maintenance_title', 'Our website is currently under construction.') }}
                    </h3>
                    <p class="text-muted mt-3 mb-3">
                        {!! nl2br(e(\App\Models\SystemSetting::getByKey('maintenance_message', 'We sincerely apologize for the inconvenience. Our site is currently undergoing scheduled maintenance and upgrades, but will return shortly.'))) !!}
                    </p>
                    
                    <h5 class="fs-14 text-stat">Don’t want to miss update? Subscribe now</h5>
                    
                    <div class="row d-flex my-4 align-items-center justify-content-center">
                        <div class="col-md-6 col-10">
                            <input type="email" class="form-control" placeholder="example@gmail.com">
                        </div>
                        <div class="col-auto mt-2 mt-md-0">
                            <button class="btn btn-primary d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-bell-ring-outline me-2"></i>Notify Me
                            </button>
                        </div>
                    </div>
                    
                    @if(Auth::check())
                        <div class="mt-4">
                            <a href="{{ route('logout') }}" class="btn btn-outline-danger btn-sm">
                                <i class="mdi mdi-logout me-1"></i>Logout
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
