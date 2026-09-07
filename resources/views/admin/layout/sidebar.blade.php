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

                <span class="brand-title">
                    DANMAQ CRM
                </span>

                <span class="brand-subtitle">
                    Admin Panel
                </span>

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


        {{-- USER MANAGEMENT --}}
        <div class="nav-section">

            <span class="nav-section-title">
                User Management
            </span>

        </div>


        {{-- USERS --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
            href="{{ route('admin.users.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-people" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Users
            </span>

        </a>


        {{-- ROLES --}}
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


        {{-- PERMISSIONS --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}"
            href="{{ route('admin.permissions.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-shield-check" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Permissions
            </span>

        </a>


        {{-- CRM --}}
        <div class="nav-section">

            <span class="nav-section-title">
                CRM
            </span>

        </div>


        {{-- LEADS --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}"
            href="{{ route('admin.leads.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-person-lines-fill" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Leads
            </span>

        </a>


        {{-- CLIENTS --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}"
            href="{{ route('admin.clients.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-person-vcard" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Clients
            </span>

        </a>


        {{-- PROJECTS --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}"
            href="{{ route('admin.projects.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-kanban" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Projects
            </span>

        </a>


        {{-- SALES --}}
        <div class="nav-section">

            <span class="nav-section-title">
                Sales & Finance
            </span>

        </div>


        {{-- QUOTATIONS --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.quotations.*') ? 'active' : '' }}"
            href="{{ route('admin.quotations.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Quotations
            </span>

        </a>


        {{-- INVOICES --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}"
            href="{{ route('admin.invoices.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-receipt" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Invoices
            </span>

        </a>


        {{-- PAYMENTS --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
            href="{{ route('admin.payments.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-credit-card" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Payments
            </span>

        </a>


        {{-- REPORTS --}}
        <div class="nav-section">

            <span class="nav-section-title">
                Reports
            </span>

        </div>


        {{-- REPORTS --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
            href="{{ route('admin.reports.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Reports
            </span>

        </a>


        {{-- PROFILE --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
            href="{{ route('admin.profile.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-person-badge" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Profile
            </span>

        </a>


        {{-- SETTINGS --}}
        {{-- <a
            class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
            href="{{ route('admin.settings.index') }}"
        > --}}

            <span class="nav-icon">
                <i class="bi bi-gear" aria-hidden="true"></i>
            </span>

            <span class="nav-text">
                Settings
            </span>

        </a>


        {{-- LOGOUT --}}
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
                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                </span>

                <span class="nav-text">
                    Logout
                </span>

            </button>

        </form>


    </nav>


    {{-- SIDEBAR FOOTER --}}
    <div class="sidebar-footer">

        <span class="status-dot"></span>

        <span class="sidebar-footer-text">
            System running smoothly
        </span>

    </div>

</aside>
