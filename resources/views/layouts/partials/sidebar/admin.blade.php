<li class="menu-title">{{ __('messages.system_management') }}</li>

<li>
    <a href="{{ route('admin.permissions.index') }}" class="tp-link">
        <i data-feather="key"></i>
        <span> {{ __('messages.roles_permissions') }} </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.users.index') }}" class="tp-link">
        <i data-feather="users"></i>
        <span> {{ __('messages.user_management') }} </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.maintenance.index') }}" class="tp-link">
        <i data-feather="cloud-off"></i>
        <span> {{ __('messages.maintenance') }} </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.notifications.index') }}" class="tp-link">
        <i data-feather="bell"></i>
        <span> {{ __('messages.notification_blast') }} </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.audit-logs.index') }}" class="tp-link">
        <i data-feather="clock"></i>
        <span> {{ __('messages.audit_trail') }} </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.tickets.index') }}" class="tp-link">
        <i data-feather="life-buoy"></i>
        <span> Support Tickets </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.backups.index') }}" class="tp-link">
        <i data-feather="hard-drive"></i>
        <span> {{ __('messages.backups') }} </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.queues.index') }}" class="tp-link">
        <i data-feather="cpu"></i>
        <span> Task Queues & Redis </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.settings.websocket.index') }}" class="tp-link">
        <i data-feather="radio"></i>
        <span> WebSocket & Pusher </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.directory.index') }}" class="tp-link">
        <i data-feather="folder"></i>
        <span> Cloud Directory (MinIO) </span>
    </a>
</li>
<li>
    <a href="{{ url('/api/documentation') }}" target="_blank" class="tp-link">
        <i data-feather="file-text"></i>
        <span> {{ __('messages.api_docs') }} </span>
    </a>
</li>