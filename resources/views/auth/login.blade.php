@extends('layouts.auth', ['title' => 'Login'])

@section('content')

<div class="col-xl-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card p-3 mb-0">
                <div class="card-body">

                    <div class="mb-0 border-0 p-md-5 p-lg-0 p-4">
                        <div class="mb-4 p-0 text-center">
                            <a href="{{ route('root') }}" class="auth-logo">
                                <img src="/images/logo-dark.png" alt="logo-dark" class="mx-auto" height="28" />
                            </a>
                        </div>

                        <div class="auth-title-section mb-3 text-center">
                            <h3 class="text-dark fs-20 fw-medium mb-2">Welcome back</h3>
                            <p class="text-dark text-capitalize fs-14 mb-0">Sign in to continue to silve.</p>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <a href="{{ route('oauth.redirect', 'google') }}" class="btn text-dark border fw-normal d-flex align-items-center justify-content-center py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48" class="me-2">
                                        <path fill="#ffc107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4C12.955 4 4 12.955 4 24s8.955 20 20 20s20-8.955 20-20c0-1.341-.138-2.65-.389-3.917" />
                                        <path fill="#ff3d00" d="m6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4C16.318 4 9.656 8.337 6.306 14.691" />
                                        <path fill="#4caf50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44" />
                                        <path fill="#1976d2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917" />
                                    </svg>
                                    <span>Google</span>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('oauth.redirect', 'github') }}" class="btn text-dark border fw-normal d-flex align-items-center justify-content-center py-2">
                                    <i class="mdi mdi-github fs-20 text-dark me-2"></i>
                                    <span>GitHub</span>
                                </a>
                            </div>
                        </div>

                        <div class="saprator my-4"><span>or continue with email</span></div>

                        <div class="pt-0">
                            <form method="POST" action="{{ route('login.authenticate') }}" class="my-4">
                                
                                @csrf
                                @if (session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>{{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if (session('status'))
                                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                                        <i class="mdi mdi-check-circle-outline me-1"></i>{{ session('status') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if (sizeof($errors) > 0)
                                @foreach ($errors->all() as $error)
                                <p class="text-danger mb-3">{{ $error }}</p>
                                @endforeach
                                @endif
                                <div class="form-group mb-3">
                                    <label for="emailaddress" class="form-label">Email address</label>
                                    <input class="form-control" type="email" name="email" id="emailaddress" required="" placeholder="Enter your email">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input class="form-control" type="password" required="" id="password" name="password" placeholder="Enter your password">
                                </div>

                                <div class="form-group d-flex mb-3">
                                    <div class="col-sm-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="checkbox-signin" checked>
                                            <label class="form-check-label" for="checkbox-signin">Remember me</label>
                                        </div>
                                    </div>
                                    @if(!\App\Models\SystemSetting::getByKey('maintenance_mode', false))
                                    <div class="col-sm-6 text-end">
                                        <a class='text-muted fs-14' href='{{ route('password.request') }}'>Forgot password?</a>
                                    </div>
                                    @endif
                                </div>

                                <div class="form-group mb-0 row">
                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button class="btn btn-primary" type="submit"> Log In </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            @if(!\App\Models\SystemSetting::getByKey('maintenance_mode', false))
                            <div class="text-center text-muted mb-4">
                                <p class="mb-0">Don't have an account ?<a class='text-primary ms-2 fw-medium' href='{{ route('register') }}'>Sign up</a></p>
                            </div>
                            @endif

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<div class="col-xl-7">
    <div class="account-page-bg p-md-5 p-4">
        <div class="text-center">
            <div class="auth-image">
                <img src="/images/auth-images.svg" class="mx-auto img-fluid" alt="images">
            </div>
        </div>
    </div>
</div>

@endsection