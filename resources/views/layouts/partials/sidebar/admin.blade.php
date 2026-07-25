@php
    /**
     * Dynamic Sidebar Permission Gate
     * --------------------------------
     * Load all permitted route_names for the current user ONCE,
     * then check visibility of each menu item in O(1) via Collection::contains().
     * Developer role bypasses everything (isDeveloper = true → all menus visible).
     */
    $user = auth()->user();
    $isDeveloper = $user && $user->isDeveloper();

    if ($isDeveloper) {
        // Developer sees everything
        $allowedRoutes = collect(['*']);
    } else {
        // Collect all route_names the user has access to via their roles
        $allowedRoutes = $user
            ? $user->roles()
                ->with('permissions:id,route_name')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('route_name')
                ->unique()
            : collect([]);
    }

    // Helper: check if user can access a given route
    $canAccess = function(string $routeName) use ($isDeveloper, $allowedRoutes) {
        if ($isDeveloper) return true;
        return $allowedRoutes->contains($routeName);
    };

    // Determine if "System Management" section title should show
    $adminRoutes = [
        'admin.permissions.index',
        'admin.users.index',
        'admin.maintenance.index',
        'admin.notifications.index',
        'admin.audit-logs.index',
        'admin.tickets.index',
        'admin.backups.index',
        'admin.queues.index',
        'admin.settings.websocket.index',
        'admin.directory.index',
        'admin.settings.branding.index',
    ];
    $hasAnyAdminAccess = $isDeveloper || $allowedRoutes->intersect($adminRoutes)->isNotEmpty();
@endphp

@if($hasAnyAdminAccess)
    <li class="menu-title">{{ __('messages.system_management') }}</li>
@endif

@if($canAccess('admin.permissions.index'))
<li>
    <a href="{{ route('admin.permissions.index') }}" class="tp-link">
        <i data-feather="key"></i>
        <span> {{ __('messages.roles_permissions') }} </span>
    </a>
</li>
@endif

@if($canAccess('admin.users.index'))
<li>
    <a href="{{ route('admin.users.index') }}" class="tp-link">
        <i data-feather="users"></i>
        <span> {{ __('messages.user_management') }} </span>
    </a>
</li>
@endif

@if($canAccess('admin.maintenance.index'))
<li>
    <a href="{{ route('admin.maintenance.index') }}" class="tp-link">
        <i data-feather="cloud-off"></i>
        <span> {{ __('messages.maintenance') }} </span>
    </a>
</li>
@endif

@if($canAccess('admin.notifications.index'))
<li>
    <a href="{{ route('admin.notifications.index') }}" class="tp-link">
        <i data-feather="bell"></i>
        <span> {{ __('messages.notification_blast') }} </span>
    </a>
</li>
@endif

@if($canAccess('admin.audit-logs.index'))
<li>
    <a href="{{ route('admin.audit-logs.index') }}" class="tp-link">
        <i data-feather="clock"></i>
        <span> {{ __('messages.audit_trail') }} </span>
    </a>
</li>
@endif

@if($canAccess('admin.tickets.index'))
<li>
    <a href="{{ route('admin.tickets.index') }}" class="tp-link">
        <i data-feather="life-buoy"></i>
        <span> {{ __('messages.support_tickets') }} </span>
    </a>
</li>
@endif

@if($canAccess('admin.backups.index'))
<li>
    <a href="{{ route('admin.backups.index') }}" class="tp-link">
        <i data-feather="hard-drive"></i>
        <span> {{ __('messages.backups') }} </span>
    </a>
</li>
@endif

@if($canAccess('admin.queues.index'))
<li>
    <a href="{{ route('admin.queues.index') }}" class="tp-link">
        <i data-feather="cpu"></i>
        <span> {{ __('messages.queues_redis') }} </span>
    </a>
</li>
@endif

@if($canAccess('admin.settings.websocket.index'))
<li>
    <a href="{{ route('admin.settings.websocket.index') }}" class="tp-link">
        <i data-feather="radio"></i>
        <span> {{ __('messages.websocket_pusher') }} </span>
    </a>
</li>
@endif

@if($canAccess('admin.directory.index'))
<li>
    <a href="{{ route('admin.directory.index') }}" class="tp-link">
        <i data-feather="folder"></i>
        <span> {{ __('messages.cloud_directory') }} </span>
    </a>
</li>
@endif

@if($canAccess('admin.settings.branding.index'))
<li>
    <a href="{{ route('admin.settings.branding.index') }}" class="tp-link">
        <i data-feather="settings"></i>
        <span> {{ __('messages.app_branding') }} </span>
    </a>
</li>
@endif

@if($hasAnyAdminAccess)
<li>
    <a href="{{ url('/api/documentation') }}" target="_blank" class="tp-link">
        <i data-feather="file-text"></i>
        <span> {{ __('messages.api_docs') }} </span>
    </a>
</li>
@endif