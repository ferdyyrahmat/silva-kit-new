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
        $allowedRoutes = collect(['*']);
    } else {
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

    $canAccess = function(string $routeName) use ($isDeveloper, $allowedRoutes) {
        if ($isDeveloper) return true;
        return $allowedRoutes->contains($routeName);
    };

    // Group visibility checks
    $showAccessControl = $canAccess('admin.permissions.index') || $canAccess('admin.users.index');
    $showOperations    = $canAccess('admin.maintenance.index') || $canAccess('admin.backups.index') || $canAccess('admin.queues.index') || $canAccess('admin.audit-logs.index');
    $showCommunication = $canAccess('admin.tickets.index') || $canAccess('admin.notifications.index');
    $showInfrastructure = $canAccess('admin.settings.websocket.index') || $canAccess('admin.directory.index');
    $showSettings      = $canAccess('admin.settings.branding.index');

    $hasAnyAdminAccess = $showAccessControl || $showOperations || $showCommunication || $showInfrastructure || $showSettings;

    // Active route detection for auto-expanding the correct submenu
    $currentRoute = request()->route() ? request()->route()->getName() : '';
@endphp

@if($hasAnyAdminAccess)
    <li class="menu-title">{{ __('messages.system_management') }}</li>
@endif

@if($isDeveloper)
<li>
    <a href="{{ route('admin.database.index') }}" class="tp-link">
        <i data-feather="database"></i>
        <span> Database Management </span>
    </a>
</li>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- ACCESS CONTROL: Roles & Permissions, User Management --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if($showAccessControl)
<li>
    <a href="#sidebarAccessControl" data-bs-toggle="collapse" aria-expanded="{{ Str::startsWith($currentRoute, 'admin.permissions.') || Str::startsWith($currentRoute, 'admin.users.') ? 'true' : 'false' }}" aria-controls="sidebarAccessControl">
        <i data-feather="shield"></i>
        <span> {{ __('messages.access_control') }} </span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse {{ Str::startsWith($currentRoute, 'admin.permissions.') || Str::startsWith($currentRoute, 'admin.users.') ? 'show' : '' }}" id="sidebarAccessControl">
        <ul class="nav-second-level">
            @if($canAccess('admin.permissions.index'))
            <li>
                <a href="{{ route('admin.permissions.index') }}" class="tp-link">{{ __('messages.roles_permissions') }}</a>
            </li>
            @endif
            @if($canAccess('admin.users.index'))
            <li>
                <a href="{{ route('admin.users.index') }}" class="tp-link">{{ __('messages.user_management') }}</a>
            </li>
            @endif
        </ul>
    </div>
</li>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- OPERATIONS: Maintenance, Backups, Queues, Audit Trail --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if($showOperations)
<li>
    <a href="#sidebarOperations" data-bs-toggle="collapse" aria-expanded="{{ Str::startsWith($currentRoute, 'admin.maintenance.') || Str::startsWith($currentRoute, 'admin.backups.') || Str::startsWith($currentRoute, 'admin.queues.') || Str::startsWith($currentRoute, 'admin.audit-logs.') ? 'true' : 'false' }}" aria-controls="sidebarOperations">
        <i data-feather="server"></i>
        <span> {{ __('messages.operations') }} </span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse {{ Str::startsWith($currentRoute, 'admin.maintenance.') || Str::startsWith($currentRoute, 'admin.backups.') || Str::startsWith($currentRoute, 'admin.queues.') || Str::startsWith($currentRoute, 'admin.audit-logs.') ? 'show' : '' }}" id="sidebarOperations">
        <ul class="nav-second-level">
            @if($canAccess('admin.maintenance.index'))
            <li>
                <a href="{{ route('admin.maintenance.index') }}" class="tp-link">{{ __('messages.maintenance') }}</a>
            </li>
            @endif
            @if($canAccess('admin.backups.index'))
            <li>
                <a href="{{ route('admin.backups.index') }}" class="tp-link">{{ __('messages.backups') }}</a>
            </li>
            @endif
            @if($canAccess('admin.queues.index'))
            <li>
                <a href="{{ route('admin.queues.index') }}" class="tp-link">{{ __('messages.queues_redis') }}</a>
            </li>
            @endif
            @if($canAccess('admin.audit-logs.index'))
            <li>
                <a href="{{ route('admin.audit-logs.index') }}" class="tp-link">{{ __('messages.audit_trail') }}</a>
            </li>
            @endif
        </ul>
    </div>
</li>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- COMMUNICATION: Support Tickets, Notification Blast --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if($showCommunication)
<li>
    <a href="#sidebarCommunication" data-bs-toggle="collapse" aria-expanded="{{ Str::startsWith($currentRoute, 'admin.tickets.') || Str::startsWith($currentRoute, 'admin.notifications.') ? 'true' : 'false' }}" aria-controls="sidebarCommunication">
        <i data-feather="message-circle"></i>
        <span> {{ __('messages.communication') }} </span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse {{ Str::startsWith($currentRoute, 'admin.tickets.') || Str::startsWith($currentRoute, 'admin.notifications.') ? 'show' : '' }}" id="sidebarCommunication">
        <ul class="nav-second-level">
            @if($canAccess('admin.tickets.index'))
            <li>
                <a href="{{ route('admin.tickets.index') }}" class="tp-link">{{ __('messages.support_tickets') }}</a>
            </li>
            @endif
            @if($canAccess('admin.notifications.index'))
            <li>
                <a href="{{ route('admin.notifications.index') }}" class="tp-link">{{ __('messages.notification_blast') }}</a>
            </li>
            @endif
        </ul>
    </div>
</li>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- INFRASTRUCTURE: WebSocket & Pusher, Cloud Directory --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if($showInfrastructure)
<li>
    <a href="#sidebarInfrastructure" data-bs-toggle="collapse" aria-expanded="{{ Str::startsWith($currentRoute, 'admin.settings.websocket.') || Str::startsWith($currentRoute, 'admin.directory.') ? 'true' : 'false' }}" aria-controls="sidebarInfrastructure">
        <i data-feather="hard-drive"></i>
        <span> {{ __('messages.infrastructure') }} </span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse {{ Str::startsWith($currentRoute, 'admin.settings.websocket.') || Str::startsWith($currentRoute, 'admin.directory.') ? 'show' : '' }}" id="sidebarInfrastructure">
        <ul class="nav-second-level">
            @if($canAccess('admin.settings.websocket.index'))
            <li>
                <a href="{{ route('admin.settings.websocket.index') }}" class="tp-link">{{ __('messages.websocket_pusher') }}</a>
            </li>
            @endif
            @if($canAccess('admin.directory.index'))
            <li>
                <a href="{{ route('admin.directory.index') }}" class="tp-link">{{ __('messages.cloud_directory') }}</a>
            </li>
            @endif
        </ul>
    </div>
</li>
@endif

{{-- ═══════════════════════════════════════════════════ --}}
{{-- SETTINGS: App Branding, API Documentation --}}
{{-- ═══════════════════════════════════════════════════ --}}
@if($showSettings || $hasAnyAdminAccess)
<li>
    <a href="#sidebarSettings" data-bs-toggle="collapse" aria-expanded="{{ Str::startsWith($currentRoute, 'admin.settings.branding.') ? 'true' : 'false' }}" aria-controls="sidebarSettings">
        <i data-feather="settings"></i>
        <span> {{ __('messages.settings') }} </span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse {{ Str::startsWith($currentRoute, 'admin.settings.branding.') ? 'show' : '' }}" id="sidebarSettings">
        <ul class="nav-second-level">
            @if($canAccess('admin.settings.branding.index'))
            <li>
                <a href="{{ route('admin.settings.branding.index') }}" class="tp-link">{{ __('messages.app_branding') }}</a>
            </li>
            @endif
            @if($hasAnyAdminAccess)
            <li>
                <a href="{{ url('/api/documentation') }}" target="_blank" class="tp-link">{{ __('messages.api_docs') }}</a>
            </li>
            @endif
        </ul>
    </div>
</li>
@endif
