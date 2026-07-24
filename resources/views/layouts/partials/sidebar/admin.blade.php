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
        <span> Maintenance </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.notifications.index') }}" class="tp-link">
        <i data-feather="bell"></i>
        <span> Notification Blast </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.audit-logs.index') }}" class="tp-link">
        <i data-feather="clock"></i>
        <span> Audit Trail </span>
    </a>
</li>
<li>
    <a href="{{ route('admin.feedbacks.index') }}" class="tp-link">
        <i data-feather="inbox"></i>
        <span> Feedbacks </span>
    </a>
</li>