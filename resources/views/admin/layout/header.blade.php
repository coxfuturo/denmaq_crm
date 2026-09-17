<nav class="navbar admin-navbar navbar-expand bg-white">
    <div class="container-fluid px-3 px-lg-4">
        <button
        class="sidebar-toggle"
        type="button"
        data-sidebar-toggle
        aria-controls="adminSidebar"
        aria-expanded="true"
        aria-label="Toggle sidebar">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <form
    class="d-none d-md-flex ms-3 flex-grow-1"
    role="search"
    method="GET"
    action="{{ route('admin.dashboard') }}">

    <input
    class="form-control search-input"
    type="search"
    name="search"
    value="{{ request('search') }}"
    placeholder="Search users, orders, reports"
    aria-label="Search">
</form>

@php
$headerUser = auth()->user();

$headerUserName = $headerUser
? trim(($headerUser->first_name ?? '') . ' ' . ($headerUser->last_name ?? ''))
: 'User';
@endphp

<div class="navbar-actions ms-auto">

    <button
    class="icon-button theme-toggle"
    type="button"
    data-theme-toggle
    aria-label="Switch color theme"
    title="Switch color theme">

    <i
    class="bi bi-moon-stars"
    data-theme-icon
    aria-hidden="true">
</i>

</button>

<div class="dropdown">

    <button
    class="icon-button"
    type="button"
    data-bs-toggle="dropdown"
    aria-expanded="false"
    aria-label="Notifications">

    <span class="notification-dot"></span>

    <i
    class="bi bi-bell"
    aria-hidden="true">
</i>

</button>

<div class="dropdown-menu dropdown-menu-end notification-menu">

    <div class="dropdown-header fw-bold text-body">
        Notifications
    </div>

    <div class="dropdown-item text-muted">
        No new notifications
    </div>

</div>

</div>

<div class="dropdown">

    <button
    class="profile-button dropdown-toggle"
    type="button"
    data-bs-toggle="dropdown"
    aria-expanded="false">

    @if($headerUser && $headerUser->profile_image)

    <img
    class="avatar-img avatar-sm"
    src="{{ asset('storage/' . $headerUser->profile_image) }}"
    alt="{{ $headerUserName }}">

    @else

    <div
    class="avatar-img avatar-sm d-flex align-items-center justify-content-center bg-primary text-white">

    {{ strtoupper(substr($headerUserName ?: 'U', 0, 1)) }}

</div>

@endif

<span class="profile-name d-none d-sm-inline">
    {{ $headerUserName ?: 'User' }}
</span>

</button>

<ul class="dropdown-menu dropdown-menu-end">

    <li>

        <div class="dropdown-item-text">

            <div class="fw-semibold text-body">
                {{ $headerUserName ?: 'User' }}
            </div>

            @if($headerUser)

            <small class="text-muted">
                {{ $headerUser->email }}
            </small>

            @endif

        </div>

    </li>

    <li>
        <hr class="dropdown-divider">
    </li>

    <li>

        <a
        class="dropdown-item"
        href="{{ route('admin.profile.edit') }}">

        <i class="bi bi-person me-2"></i>
        Profile

    </a>

</li>

<li>
    <hr class="dropdown-divider">
</li>

<li>

    <form
    action="{{ route('logout') }}"
    method="POST"
    class="logout-form"
    id="headerLogoutForm">

    @csrf

    <button
    type="button"
    class="dropdown-item logout-link"
    onclick="return headerLogoutConfirm();">

    <i class="bi bi-box-arrow-right me-2"></i>
    Sign out

</button>

</form>

</li>

</ul>

</div>

</div>

</div>
</nav>
