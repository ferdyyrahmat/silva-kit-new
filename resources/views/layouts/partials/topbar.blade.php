<!-- Topbar Start -->
<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button class="button-toggle-menu nav-link">
                        <i data-feather="menu" class="noti-icon"></i>
                    </button>
                </li>
                <li class="d-none d-lg-block">
                    <h5 class="mb-0">{{ __('messages.good_morning') }}, {{ auth()->user()->name }}</h5>
                </li>
                @if(\App\Models\SystemSetting::getByKey('maintenance_mode', false))
                <li class="ms-lg-3 topbar-maintenance-item">
                    <span class="badge bg-danger-subtle text-danger border border-danger d-inline-flex align-items-center px-2 py-1 fs-12 fw-semibold topbar-maintenance-badge">
                        <span class="spinner-grow spinner-grow-sm text-danger me-1" role="status" style="width: 8px; height: 8px; animation-duration: 1.2s;"></span>
                        MAINTENANCE MODE ACTIVE
                    </span>
                </li>
                
                <style>
                    @media (max-width: 991.98px) {
                        .topbar-maintenance-item {
                            position: fixed;
                            top: 15px;
                            left: 50%;
                            transform: translateX(-50%);
                            z-index: 1060;
                            margin: 0 !important;
                            pointer-events: none;
                        }
                        .topbar-maintenance-badge {
                            box-shadow: 0 4px 10px rgba(239, 71, 111, 0.2);
                            font-size: 11px !important;
                            padding: 6px 12px !important;
                            border-radius: 30px !important;
                            background-color: rgba(239, 71, 111, 0.95) !important;
                            color: #ffffff !important;
                            border: 1px solid #ef476f !important;
                            animation: pulse-fade 2s infinite ease-in-out;
                        }
                        .topbar-maintenance-badge .spinner-grow {
                            color: #ffffff !important;
                        }
                    }
                    @keyframes pulse-fade {
                        0%, 100% {
                            opacity: 0.15;
                        }
                        50% {
                            opacity: 1;
                        }
                    }
                </style>
                @endif
            </ul>

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">

                <!-- Global Search Bar -->
                <li class="d-none d-lg-block position-relative me-2">
                    <div class="position-relative topbar-search">
                        <input type="text" id="topbar-search-input" class="form-control bg-light bg-opacity-75 border-light ps-4" placeholder="{{ __('messages.search') }}" autocomplete="off">
                        <i class="mdi mdi-magnify fs-16 position-absolute text-muted top-50 translate-middle-y ms-2"></i>
                    </div>

                    <!-- Search Results Dropdown -->
                    <div id="topbar-search-dropdown" class="dropdown-menu dropdown-menu-start shadow-lg border mt-2 p-0 w-100 overflow-hidden" style="display: none; min-width: 320px; max-height: 400px; overflow-y: auto; z-index: 1050;">
                        <div id="topbar-search-content" class="p-2"></div>
                    </div>
                </li>

                <!-- Theme Toggle (Dark/Light Mode) -->
                <li class="d-none d-sm-flex me-1">
                    <button type="button" class="btn nav-link" id="btn-theme-toggle" title="{{ session('theme') === 'dark' ? __('messages.light_mode') : __('messages.dark_mode') }}">
                        <i id="theme-toggle-icon" class="mdi {{ session('theme') === 'dark' ? 'mdi-weather-sunny' : 'mdi-weather-night' }} fs-20 align-middle"></i>
                    </button>
                </li>

                <!-- Direct Language Switcher Toggle (ID / EN) -->
                <li class="d-none d-sm-flex me-1">
                    <a href="{{ route('lang.switch', app()->getLocale() === 'id' ? 'en' : 'id') }}" class="btn nav-link fw-bold fs-13 d-flex align-items-center" title="{{ app()->getLocale() === 'id' ? 'Switch to English' : 'Ubah ke Bahasa Indonesia' }}">
                        <span class="badge bg-body-secondary text-body border px-2 py-1 fs-12 text-uppercase">{{ app()->getLocale() }}</span>
                    </a>
                </li>

                <li class="d-none d-sm-flex">
                    <button type="button" class="btn nav-link" data-toggle="fullscreen">
                        <i data-feather="maximize" class="align-middle fullscreen noti-icon"></i>
                    </button>
                </li>

                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i data-feather="bell" class="noti-icon"></i>
                        <span class="badge bg-danger rounded-circle noti-icon-badge">9</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-lg">

                        <!-- item-->
                        <div class="dropdown-item noti-title">
                            <h5 class="m-0">
                                <span class="float-end">
                                    <a href="" class="text-dark">
                                        <small>{{ __('messages.clear_all') }}</small>
                                    </a>
                                </span>{{ __('messages.notifications') }}
                            </h5>
                        </div>

                        <div class="noti-scroll" data-simplebar>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary active">
                                <div class="notify-icon">
                                    <img src="/images/users/user-12.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="notify-details">Carl Steadham</p>
                                    <small class="text-muted">5 min ago</small>
                                </div>
                                <p class="mb-0 user-msg">
                                    <small class="fs-14">Completed <span class="text-reset">Improve workflow in Figma</span></small>
                                </p>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary">
                                <div class="notify-icon">
                                    <img src="/images/users/user-2.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="notify-content">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="notify-details">Olivia McGuire</p>
                                        <small class="text-muted">1 min ago</small>
                                    </div>

                                    <div class="d-flex mt-2 align-items-center">
                                        <div class="notify-sub-icon">
                                            <i class="mdi mdi-download-box text-dark"></i>
                                        </div>

                                        <div>
                                            <p class="notify-details mb-0">dark-themes.zip</p>
                                            <small class="text-muted">2.4 MB</small>
                                        </div>
                                    </div>

                                </div>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary">
                                <div class="notify-icon">
                                    <img src="/images/users/user-3.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="notify-content">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="notify-details">Travis Williams</p>
                                        <small class="text-muted">7 min ago</small>
                                    </div>
                                    <p class="noti-mentioned p-2 rounded-2 mb-0 mt-2"><span class="text-primary">@Patryk</span> Please make sure that you're....</p>
                                </div>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary">
                                <div class="notify-icon">
                                    <img src="/images/users/user-8.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="notify-details">Violette Lasky</p>
                                    <small class="text-muted">5 min ago</small>
                                </div>
                                <p class="mb-0 user-msg">
                                    <small class="fs-14">Completed <span class="text-reset">Create new components</span></small>
                                </p>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary">
                                <div class="notify-icon">
                                    <img src="/images/users/user-5.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="notify-details">Ralph Edwards</p>
                                    <small class="text-muted">5 min ago</small>
                                </div>
                                <p class="mb-0 user-msg">
                                    <small class="fs-14">Completed <span class="text-reset">Improve workflow in React</span></small>
                                </p>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item text-muted link-primary">
                                <div class="notify-icon">
                                    <img src="/images/users/user-6.jpg" class="img-fluid rounded-circle" alt="" />
                                </div>
                                <div class="notify-content">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="notify-details">Jocab jones</p>
                                        <small class="text-muted">7 min ago</small>
                                    </div>
                                    <p class="noti-mentioned p-2 rounded-2 mb-0 mt-2"><span class="text-reset">@Patryk</span> Please make sure that you're....</p>
                                </div>
                            </a>
                        </div>

                        <!-- All-->
                        <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all">
                            {{ __('messages.view_all') }}
                            <i class="fe-arrow-right"></i>
                        </a>

                    </div>
                </li>

                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ auth()->user()->avatar_url }}" alt="user-image" class="rounded-circle" width="32" height="32" style="object-fit: cover; aspect-ratio: 1/1;">
                        <span class="pro-user-name ms-1">
                            {{ auth()->user()->name }} <i class="mdi mdi-chevron-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">{{ __('messages.welcome') }} !</h6>
                        </div>

                        <!-- item-->
                        <a href="{{ route('v1.profile.index') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                            <span>{{ __('messages.my_account') }}</span>
                        </a>

                        <!-- item-->
                        <form id="lockscreen-form" action="{{ route('lockscreen.lock') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <a href="javascript:void(0);" onclick="document.getElementById('lockscreen-form').submit();" class="dropdown-item notify-item">
                            <i class="mdi mdi-lock-outline fs-16 align-middle"></i>
                            <span>{{ __('messages.lock_screen') }}</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a href="{{ url('/logout') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                            <span>{{ __('messages.logout') }}</span>
                        </a>

                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>
<!-- end Topbar -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dark Mode Toggle
        var btnToggle = document.getElementById('btn-theme-toggle');
        if (btnToggle) {
            btnToggle.addEventListener('click', function(e) {
                e.preventDefault();
                var html = document.documentElement;
                var currentTheme = html.getAttribute('data-bs-theme') || 'light';
                var newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                html.setAttribute('data-bs-theme', newTheme);
                document.body.setAttribute('data-menu-color', newTheme);

                var icon = document.getElementById('theme-toggle-icon');
                if (icon) {
                    icon.className = newTheme === 'dark' ? 'mdi mdi-weather-sunny fs-20 align-middle' : 'mdi mdi-weather-night fs-20 align-middle';
                }

                $.ajax({
                    url: "{{ route('theme.toggle') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        theme: newTheme
                    }
                });
            });
        }

        // Live Global Search Bar
        var searchInput = document.getElementById('topbar-search-input');
        var searchDropdown = document.getElementById('topbar-search-dropdown');
        var searchContent = document.getElementById('topbar-search-content');
        var searchTimer = null;

        if (searchInput && searchDropdown && searchContent) {
            searchInput.addEventListener('input', function() {
                var q = this.value.trim();
                clearTimeout(searchTimer);

                if (q.length < 2) {
                    searchDropdown.style.display = 'none';
                    return;
                }

                searchTimer = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('global.search') }}",
                        type: "GET",
                        data: { q: q },
                        success: function(res) {
                            if (res.success && res.results.length > 0) {
                                var html = '';
                                var currentCategory = '';

                                res.results.forEach(function(item) {
                                    if (item.category !== currentCategory) {
                                        currentCategory = item.category;
                                        html += '<div class="dropdown-header text-uppercase fs-11 fw-bold text-muted py-1 px-3 mt-1">' + currentCategory + '</div>';
                                    }

                                    var avatarHtml = item.avatar 
                                        ? '<img src="' + item.avatar + '" class="rounded-circle me-2" width="24" height="24" style="object-fit: cover;">'
                                        : '<i class="mdi ' + (item.icon || 'mdi-magnify') + ' me-2 fs-16 text-primary align-middle"></i>';

                                    html += '<a href="' + item.url + '" class="dropdown-item d-flex align-items-center py-2 px-3 rounded-2 mb-1 search-result-item">';
                                    html += avatarHtml;
                                    html += '<div class="overflow-hidden">';
                                    html += '<div class="fw-semibold text-body fs-13 text-truncate">' + item.title + '</div>';
                                    html += '<div class="fs-11 text-muted text-truncate">' + item.subtitle + '</div>';
                                    html += '</div>';
                                    html += '</a>';
                                });

                                searchContent.innerHTML = html;
                                searchDropdown.style.display = 'block';
                            } else {
                                searchContent.innerHTML = '<div class="p-3 text-center text-muted fs-13"><i class="mdi mdi-alert-circle-outline me-1"></i>No results found</div>';
                                searchDropdown.style.display = 'block';
                            }
                        }
                    });
                }, 250);
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                    searchDropdown.style.display = 'none';
                }
            });

            // Hide dropdown on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchDropdown.style.display = 'none';
                }
            });
        }
    });
</script>