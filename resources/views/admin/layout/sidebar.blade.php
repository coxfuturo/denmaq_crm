<aside class="admin-sidebar" id="adminSidebar" aria-label="Main navigation">
{{-- SIDEBAR HEADER --}}
<div class="sidebar-header">
    <a
        class="brand-mark"
        href="{{ route('admin.dashboard') }}"
        aria-label="CRM Dashboard"
    >
        <span class="brand-icon">
            <i class="bi bi-grid-1x2-fill" aria-hidden="true"></i>
        </span>

        <span class="brand-copy">
            <span class="brand-title">DANMAQ CRM</span>
            <span class="brand-subtitle">Admin Panel</span>
        </span>
    </a>
</div>


{{-- SIDEBAR NAVIGATION --}}
<nav class="sidebar-nav">

    {{-- DASHBOARD --}}
    <a
        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
        href="{{ route('admin.dashboard') }}"
    >
        <span class="nav-icon">
            <i class="bi bi-speedometer2" aria-hidden="true"></i>
        </span>

        <span class="nav-text">
            Dashboard
        </span>
    </a>


    {{-- ================= USER MANAGEMENT ================= --}}
    <div class="nav-section">
        <span class="nav-section-title">
            User Management
        </span>
    </div>


    {{-- USERS --}}
    @if(Route::has('admin.users.index'))
        <a
            class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
            href="{{ route('admin.users.index') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-people" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Users
            </span>
        </a>
    @endif


    {{-- ROLES --}}
    @if(Route::has('admin.roles.index'))
        <a
            class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
            href="{{ route('admin.roles.index') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-person-gear" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Roles
            </span>
        </a>
    @endif


    {{-- PERMISSIONS --}}
    @if(Route::has('admin.permissions.index'))
        <a
            class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}"
            href="{{ route('admin.permissions.index') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-shield-check" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Permissions
            </span>
        </a>
    @endif



    {{-- ================= CRM ================= --}}
    <div class="nav-section">
        <span class="nav-section-title">
            CRM
        </span>
    </div>


    {{-- LEADS --}}
    @if(Route::has('admin.leads.index'))
        <a
            class="nav-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}"
            href="{{ route('admin.leads.index') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-person-lines-fill" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Leads
            </span>
        </a>
    @endif


    {{-- CLIENTS --}}
    @if(Route::has('admin.clients.index'))
        <a
            class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}"
            href="{{ route('admin.clients.index') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-person-vcard" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Clients
            </span>
        </a>
    @endif


    {{-- PROJECTS --}}
    @if(Route::has('admin.projects.index'))
        <a
            class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}"
            href="{{ route('admin.projects.index') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-kanban" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Projects
            </span>
        </a>
    @endif



    {{-- ================= SALES & FINANCE ================= --}}
    <div class="nav-section">
        <span class="nav-section-title">
            Sales & Finance
        </span>
    </div>


    {{-- QUOTATIONS --}}
    @if(Route::has('admin.quotations.index'))
        <a
            class="nav-link {{ request()->routeIs('admin.quotations.*') ? 'active' : '' }}"
            href="{{ route('admin.quotations.index') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Quotations
            </span>
        </a>
    @endif


    {{-- INVOICES --}}
    @if(Route::has('admin.invoices.index'))
        <a
            class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}"
            href="{{ route('admin.invoices.index') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-receipt" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Invoices
            </span>
        </a>
    @endif


    {{-- PAYMENTS --}}
    @if(Route::has('admin.payments.index'))
        <a
            class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
            href="{{ route('admin.payments.index') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-credit-card" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Payments
            </span>
        </a>
    @endif



    {{-- ================= REPORTS ================= --}}
    <div class="nav-section">
        <span class="nav-section-title">
            Reports
        </span>
    </div>


    {{-- REPORTS --}}
    @if(Route::has('admin.reports.index'))
        <a
            class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
            href="{{ route('admin.reports.index') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Reports
            </span>
        </a>
    @endif



    {{-- ================= SETTINGS ================= --}}
    <div class="nav-section">
        <span class="nav-section-title">
            System
        </span>
    </div>


    {{-- PROFILE --}}
    @if(Route::has('admin.profile'))
        <a
            class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}"
            href="{{ route('admin.profile') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-person-badge" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Profile
            </span>
        </a>
    @endif


    {{-- SETTINGS --}}
    @if(Route::has('admin.settings'))
        <a
            class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}"
            href="{{ route('admin.settings') }}"
        >
            <span class="nav-icon">
                <i class="bi bi-gear" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Settings
            </span>
        </a>
    @endif



    {{-- ================= DYNAMIC ROLE PERMISSIONS ================= --}}
    @php
        $userRoles = getUserRoles();
    @endphp

    @if($userRoles->count() > 0)

        <div class="nav-section">
            <span class="nav-section-title">
                Assigned Permissions
            </span>
        </div>


        @foreach($userRoles as $role)

            @php
                $permissions = getAssignedPermissions($role->id);
            @endphp


            @if($permissions->count() > 0)

                {{-- ROLE --}}
                <div class="nav-section">
                    <span class="nav-section-title">

                        @php
                            $roleIcon = 'shield';

                            switch ($role->icon) {
                                case 'shield':
                                    $roleIcon = 'shield';
                                    break;

                                case 'user-check':
                                    $roleIcon = 'person-check';
                                    break;

                                case 'users':
                                    $roleIcon = 'people';
                                    break;

                                case 'briefcase':
                                    $roleIcon = 'briefcase';
                                    break;

                                case 'user':
                                    $roleIcon = 'person';
                                    break;

                                case 'dollar-sign':
                                    $roleIcon = 'currency-dollar';
                                    break;

                                case 'headphones':
                                    $roleIcon = 'headphones';
                                    break;

                                case 'eye':
                                    $roleIcon = 'eye';
                                    break;

                                default:
                                    $roleIcon = 'shield';
                                    break;
                            }
                        @endphp

                        <i
                            class="bi bi-{{ $roleIcon }}"
                            aria-hidden="true"
                        ></i>

                        {{ $role->name }}

                    </span>
                </div>


                {{-- ROLE PERMISSIONS --}}
                @foreach($permissions as $permission)

                    @if(
                        !empty($permission->route) &&
                        Route::has($permission->route)
                    )

                        @php

                            $permissionIcon = 'circle';

                            switch ($permission->name) {

                                case 'Users':
                                    $permissionIcon = 'people';
                                    break;

                                case 'Roles':
                                    $permissionIcon = 'person-gear';
                                    break;

                                case 'Permissions':
                                    $permissionIcon = 'shield-check';
                                    break;

                                case 'Leads':
                                    $permissionIcon = 'person-lines-fill';
                                    break;

                                case 'Clients':
                                    $permissionIcon = 'person-vcard';
                                    break;

                                case 'Projects':
                                    $permissionIcon = 'kanban';
                                    break;

                                case 'Quotations':
                                    $permissionIcon = 'file-earmark-text';
                                    break;

                                case 'Invoices':
                                    $permissionIcon = 'receipt';
                                    break;

                                case 'Payments':
                                    $permissionIcon = 'credit-card';
                                    break;

                                case 'Reports':
                                    $permissionIcon = 'bar-chart-line';
                                    break;

                                case 'Profile':
                                    $permissionIcon = 'person-badge';
                                    break;

                                case 'Settings':
                                    $permissionIcon = 'gear';
                                    break;

                                default:
                                    $permissionIcon = 'circle';
                                    break;
                            }

                        @endphp

                        <a
                            class="nav-link {{ request()->routeIs($permission->route) || request()->routeIs(str_replace('.index', '.*', $permission->route)) ? 'active' : '' }}"
                            href="{{ route($permission->route) }}"
                        >

                            <span class="nav-icon">

                                <i
                                    class="bi bi-{{ $permissionIcon }}"
                                    aria-hidden="true"
                                ></i>

                            </span>

                            <span class="nav-text">
                                {{ $permission->name }}
                            </span>

                        </a>

                    @endif

                @endforeach

            @endif

        @endforeach

    @endif

    <form
        action="{{ route('logout') }}"
        method="POST"
        class="logout-form"
    >

        @csrf

        <button
            type="submit"
            class="nav-link logout-link"
        >

            <span class="nav-icon">
                <i
                    class="bi bi-box-arrow-right"
                    aria-hidden="true"
                ></i>
            </span>

            <span class="nav-text">
                Logout
            </span>

        </button>

    </form>

</nav>

<div class="sidebar-footer">

    <span class="status-dot"></span>

    <span class="sidebar-footer-text">
        System running smoothly
    </span>

</div>
</aside>
