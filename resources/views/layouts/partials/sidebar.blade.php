<!-- Left Sidebar Start -->
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

@php
    $logoLight = \App\Models\SystemSetting::getByKey('app_logo_light', '/images/logo-light.png');
    $logoDark = \App\Models\SystemSetting::getByKey('app_logo_dark', '/images/logo-dark.png');
    $logoSm = \App\Models\SystemSetting::getByKey('app_logo_sm', '/images/logo-sm.png');
@endphp
            <div class="logo-box">
                <a href="{{ route('root')}}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ $logoSm }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ $logoLight }}" alt="" height="24">
                    </span>
                </a>
                <a href="{{ route('root')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ $logoSm }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ $logoDark }}" alt="" height="24">
                    </span>
                </a>
            </div>

            <ul id="side-menu">
                <li>
                    <a href="{{ route('root')}}" class="tp-link">
                        <i data-feather="home"></i>
                        <span> {{ __('messages.dashboard') }} </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('v1.tickets.index') }}" class="tp-link">
                        <i data-feather="life-buoy"></i>
                        <span> {{ __('messages.my_tickets') }} </span>
                    </a>
                </li>

                @include('layouts.partials.sidebar.admin')
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
<!-- Left Sidebar End -->