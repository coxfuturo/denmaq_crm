```blade
<aside class="admin-sidebar" id="adminSidebar" aria-label="Main navigation">

    <div class="sidebar-header">
        <a class="brand-mark" href="{{ route('admin.dashboard') }}" aria-label="CRM Dashboard">
            <span class="brand-icon">
                <i class="bi bi-grid-1x2-fill"></i>
            </span>

            <span class="brand-copy">
                <span class="brand-title">DANMAQ CRM</span>
                <span class="brand-subtitle">Admin Panel</span>
            </span>
        </a>
    </div>


    <nav class="sidebar-nav">

        {{-- ================= DASHBOARD ================= --}}
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}">

            <span class="nav-icon">
                <i class="bi bi-speedometer2"></i>
            </span>

            <span class="nav-text">Dashboard</span>
        </a>


       {{-- ================= CRM ================= --}}
@php
    $crmOpen =
        request()->routeIs('admin.leads.*') ||
        request()->routeIs('admin.clients.*') ||
        request()->routeIs('admin.followups.*') ||
        request()->routeIs('admin.projects.*');
@endphp

<div class="nav-dropdown">

    {{-- CRM Parent --}}
    <a href="#sidebarCRM"
       class="nav-link nav-dropdown-toggle {{ $crmOpen ? '' : 'collapsed' }}"
       data-bs-toggle="collapse"
       role="button"
       aria-expanded="{{ $crmOpen ? 'true' : 'false' }}"
       aria-controls="sidebarCRM">

        <span class="nav-icon">
            <i class="bi bi-briefcase-fill"></i>
        </span>

        <span class="nav-text">
            CRM
        </span>

        <i class="bi bi-chevron-down dropdown-arrow"></i>

    </a>


    {{-- CRM Children --}}
    <div class="collapse {{ $crmOpen ? 'show' : '' }}"
         id="sidebarCRM"
         data-bs-parent=".sidebar-nav">

        <div class="nav-dropdown-menu">

            {{-- ================= LEADS ================= --}}
            @if(Route::has('admin.leads.index'))
                <a href="{{ route('admin.leads.index') }}"
                   class="nav-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">

                    <span class="nav-icon">
                        <i class="bi bi-person-lines-fill"></i>
                    </span>

                    <span class="nav-text">
                        Leads
                    </span>

                </a>
            @endif


            {{-- ================= CLIENTS ================= --}}
            @if(Route::has('admin.clients.index'))
                <a href="{{ route('admin.clients.index') }}"
                   class="nav-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">

                    <span class="nav-icon">
                        <i class="bi bi-person-vcard"></i>
                    </span>

                    <span class="nav-text">
                        Clients
                    </span>

                </a>
            @endif


            {{-- ================= FOLLOW UPS ================= --}}
            @if(Route::has('admin.followups.index'))
                <a href="{{ route('admin.followups.index') }}"
                   class="nav-link {{ request()->routeIs('admin.followups.*') ? 'active' : '' }}">

                    <span class="nav-icon">
                        <i class="bi bi-calendar-check"></i>
                    </span>

                    <span class="nav-text">
                        Follow Ups
                    </span>

                </a>
            @endif


            {{-- ================= PROJECTS ================= --}}
            @if(Route::has('admin.projects.index'))
                <a href="{{ route('admin.projects.index') }}"
                   class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">

                    <span class="nav-icon">
                        <i class="bi bi-kanban"></i>
                    </span>

                    <span class="nav-text">
                        Projects
                    </span>

                </a>
            @endif

        </div>

    </div>

</div>

        {{-- ================= USER MANAGEMENT ================= --}}
        @php
            $userManagementOpen =
                request()->routeIs('admin.users.*') ||
                request()->routeIs('admin.roles.*') ||
                request()->routeIs('admin.permissions.*');
        @endphp

        <div class="nav-dropdown">

            <a href="#sidebarUserManagement"
               class="nav-link nav-dropdown-toggle {{ $userManagementOpen ? '' : 'collapsed' }}"
               data-bs-toggle="collapse"
               role="button"
               aria-expanded="{{ $userManagementOpen ? 'true' : 'false' }}"
               aria-controls="sidebarUserManagement">

                <span class="nav-icon">
                    <i class="bi bi-people-fill"></i>
                </span>

                <span class="nav-text">User Management</span>

                <i class="bi bi-chevron-down dropdown-arrow"></i>
            </a>


            <div class="collapse {{ $userManagementOpen ? 'show' : '' }}"
                 id="sidebarUserManagement"
                 data-bs-parent=".sidebar-nav">

                <div class="nav-dropdown-menu">

                    @if(Route::has('admin.users.index'))
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                           href="{{ route('admin.users.index') }}">

                            <span class="nav-icon">
                                <i class="bi bi-people"></i>
                            </span>

                            <span class="nav-text">Users</span>
                        </a>
                    @endif


                    @if(Route::has('admin.roles.index'))
                        <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                           href="{{ route('admin.roles.index') }}">

                            <span class="nav-icon">
                                <i class="bi bi-person-gear"></i>
                            </span>

                            <span class="nav-text">Roles</span>
                        </a>
                    @endif


                    @if(Route::has('admin.permissions.index'))
                        <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}"
                           href="{{ route('admin.permissions.index') }}">

                            <span class="nav-icon">
                                <i class="bi bi-shield-check"></i>
                            </span>

                            <span class="nav-text">Permissions</span>
                        </a>
                    @endif

                </div>
            </div>
        </div>


        {{-- ================= SALES ================= --}}
        @php
            $salesOpen =
                request()->routeIs('admin.quotations.*') ||
                request()->routeIs('admin.invoices.*') ||
                request()->routeIs('admin.payments.*');
        @endphp

        <div class="nav-dropdown">

            <a href="#sidebarSales"
               class="nav-link nav-dropdown-toggle {{ $salesOpen ? '' : 'collapsed' }}"
               data-bs-toggle="collapse"
               role="button"
               aria-expanded="{{ $salesOpen ? 'true' : 'false' }}"
               aria-controls="sidebarSales">

                <span class="nav-icon">
                    <i class="bi bi-cart-check-fill"></i>
                </span>

                <span class="nav-text">Sales</span>

                <i class="bi bi-chevron-down dropdown-arrow"></i>
            </a>


            <div class="collapse {{ $salesOpen ? 'show' : '' }}"
                 id="sidebarSales"
                 data-bs-parent=".sidebar-nav">

                <div class="nav-dropdown-menu">

                    @if(Route::has('admin.quotations.index'))
                        <a class="nav-link {{ request()->routeIs('admin.quotations.*') ? 'active' : '' }}"
                           href="{{ route('admin.quotations.index') }}">

                            <span class="nav-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </span>

                            <span class="nav-text">Quotations</span>
                        </a>
                    @endif


                    @if(Route::has('admin.invoices.index'))
                        <a class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}"
                           href="{{ route('admin.invoices.index') }}">

                            <span class="nav-icon">
                                <i class="bi bi-receipt"></i>
                            </span>

                            <span class="nav-text">Invoices</span>
                        </a>
                    @endif


                    @if(Route::has('admin.payments.index'))
                        <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
                           href="{{ route('admin.payments.index') }}">

                            <span class="nav-icon">
                                <i class="bi bi-credit-card"></i>
                            </span>

                            <span class="nav-text">Payments</span>
                        </a>
                    @endif

                </div>
            </div>
        </div>


        {{-- ================= REPORTS ================= --}}
        @php
            $reportsOpen =
                request()->routeIs('admin.reports.leads') ||
                request()->routeIs('admin.reports.sales') ||
                request()->routeIs('admin.reports.users');
        @endphp

        <div class="nav-dropdown">

            <a href="#sidebarReports"
               class="nav-link nav-dropdown-toggle {{ $reportsOpen ? '' : 'collapsed' }}"
               data-bs-toggle="collapse"
               role="button"
               aria-expanded="{{ $reportsOpen ? 'true' : 'false' }}"
               aria-controls="sidebarReports">

                <span class="nav-icon">
                    <i class="bi bi-bar-chart-fill"></i>
                </span>

                <span class="nav-text">Reports</span>

                <i class="bi bi-chevron-down dropdown-arrow"></i>
            </a>


            <div class="collapse {{ $reportsOpen ? 'show' : '' }}"
                 id="sidebarReports"
                 data-bs-parent=".sidebar-nav">

                <div class="nav-dropdown-menu">

                    @if(Route::has('admin.reports.leads'))
                        <a class="nav-link {{ request()->routeIs('admin.reports.leads') ? 'active' : '' }}"
                           href="{{ route('admin.reports.leads') }}">

                            <span class="nav-icon">
                                <i class="bi bi-person-lines-fill"></i>
                            </span>

                            <span class="nav-text">Lead Report</span>
                        </a>
                    @endif


                    @if(Route::has('admin.reports.sales'))
                        <a class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}"
                           href="{{ route('admin.reports.sales') }}">

                            <span class="nav-icon">
                                <i class="bi bi-graph-up-arrow"></i>
                            </span>

                            <span class="nav-text">Sales Report</span>
                        </a>
                    @endif


                    @if(Route::has('admin.reports.users'))
                        <a class="nav-link {{ request()->routeIs('admin.reports.users') ? 'active' : '' }}"
                           href="{{ route('admin.reports.users') }}">

                            <span class="nav-icon">
                                <i class="bi bi-people"></i>
                            </span>

                            <span class="nav-text">User Report</span>
                        </a>
                    @endif

                </div>
            </div>
        </div>


        {{-- ================= SETTINGS ================= --}}
        @php
            $settingsOpen =
                request()->routeIs('admin.settings') ||
                request()->routeIs('admin.profile');
        @endphp

        <div class="nav-dropdown">

            <a href="#sidebarSettings"
               class="nav-link nav-dropdown-toggle {{ $settingsOpen ? '' : 'collapsed' }}"
               data-bs-toggle="collapse"
               role="button"
               aria-expanded="{{ $settingsOpen ? 'true' : 'false' }}"
               aria-controls="sidebarSettings">

                <span class="nav-icon">
                    <i class="bi bi-gear-fill"></i>
                </span>

                <span class="nav-text">Settings</span>

                <i class="bi bi-chevron-down dropdown-arrow"></i>
            </a>


            <div class="collapse {{ $settingsOpen ? 'show' : '' }}"
                 id="sidebarSettings"
                 data-bs-parent=".sidebar-nav">

                <div class="nav-dropdown-menu">

                    @if(Route::has('admin.settings'))
                        <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}"
                           href="{{ route('admin.settings') }}">

                            <span class="nav-icon">
                                <i class="bi bi-building"></i>
                            </span>

                            <span class="nav-text">Company Settings</span>
                        </a>
                    @endif


                    @if(Route::has('admin.profile'))
                        <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}"
                           href="{{ route('admin.profile') }}">

                            <span class="nav-icon">
                                <i class="bi bi-person-badge"></i>
                            </span>

                            <span class="nav-text">Profile</span>
                        </a>
                    @endif

                </div>
            </div>
        </div>


        {{-- ================= DIVIDER ================= --}}
        <div class="nav-divider"></div>


        {{-- ================= LOGOUT ================= --}}
        <form action="{{ route('logout') }}"
              method="POST"
              class="logout-form">

            @csrf

            <button type="submit" class="nav-link logout-link">

                <span class="nav-icon">
                    <i class="bi bi-box-arrow-right"></i>
                </span>

                <span class="nav-text">Logout</span>

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
```
