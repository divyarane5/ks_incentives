<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            @php
                $logoPath = isset($activeBusinessUnit) && $activeBusinessUnit->logo_path
                    ? asset('storage/' . str_replace('public/', '', $activeBusinessUnit->logo_path))
                    : asset('assets/img/logo/ks-logos.webp');
            @endphp
            <img src="{{ $logoPath }}" alt="Logo" class="img-fluid">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ ((Request::segment(1) == 'dashboard')||(Request::segment(1) == '')) ? 'active': '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        @can('user-view')
        <li class="menu-item {{ (Request::segment(1) == 'users') ? 'active': '' }}">
            <a href="{{ route('users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Analytics">User</div>
            </a>
        </li>
        @endcan
        <!-- Masters -->
        @canany(['role-view','location-view','department-view','designation-view','expense-view','vendor-view','business_unit-view','payment_method-view'])
        <li class="menu-item {{ in_array(Request::segment(1), ['role','location','department','designation','expense','vendor','business_unit','payment_method']) ? 'active open': '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Masters</div>
            </a>

            @can('role-view')
            <ul class="menu-sub">
                <li class="menu-item {{ ((Request::segment(1) == 'role')) ? 'active': '' }}">
                    <a href="{{ route('role.index') }}" class="menu-link">
                        <div data-i18n="Without menu">Role</div>
                    </a>
                </li>
            </ul>
            @endcan

            @can('location-view')
            <ul class="menu-sub">
                <li class="menu-item {{ ((Request::segment(1) == 'location')) ? 'active': '' }}">
                    <a href="{{ route('location.index') }}" class="menu-link">
                        <div data-i18n="Without menu">Location</div>
                    </a>
                </li>
            </ul>
            @endcan

            @can('department-view')
            <ul class="menu-sub">
                <li class="menu-item {{ ((Request::segment(1) == 'department')) ? 'active': '' }}">
                    <a href="{{ route('department.index') }}" class="menu-link">
                        <div data-i18n="Without menu">Department</div>
                    </a>
                </li>
            </ul>
            @endcan

            @can('designation-view')
            <ul class="menu-sub">
                <li class="menu-item {{ ((Request::segment(1) == 'designation')) ? 'active': '' }}">
                    <a href="{{ route('designation.index') }}" class="menu-link">
                        <div data-i18n="Without menu">Designation</div>
                    </a>
                </li>
            </ul>
            @endcan

            @can('business_unit-view')
            <ul class="menu-sub">
                <li class="menu-item {{ ((Request::segment(1) == 'business_unit')) ? 'active': '' }}">
                    <a href="{{ route('business_unit.index') }}" class="menu-link">
                        <div data-i18n="Without menu">Business Unit</div>
                    </a>
                </li>
            </ul>
            @endcan
        </li>
        @endcanany

        
        <!-- Mandate Section -->
        <!-- Mandate Section -->
        @canany(['mandate_project-view','channel-partner-view','client-enquiries-view'])
        <li class="menu-item {{ in_array(Request::segment(1), ['mandate_projects','channel_partners','client-enquiries']) ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-briefcase"></i>
                <div data-i18n="Layouts">Mandate</div>
            </a>
            <ul class="menu-sub">
               

                @can('mandate_project-view')
                <li class="menu-item {{ (Request::segment(1) == 'mandate_projects') ? 'active' : '' }}">
                    <a href="{{ route('mandate_projects.index') }}" class="menu-link">
                        <div data-i18n="Without menu">Mandate Projects</div>
                    </a>
                </li>
                @endcan

                @can('channel-partner-view')
                <li class="menu-item {{ (Request::segment(1) == 'channel_partners') ? 'active' : '' }}">
                    <a href="{{ route('channel_partners.index') }}" class="menu-link">
                        <div data-i18n="Without menu">Channel Partner</div>
                    </a>
                </li>
                @endcan

                @can('client-enquiry-view')
                <li class="menu-item {{ (Request::segment(1) == 'client-enquiry') ? 'active' : '' }}">
                    <a href="{{ route('client-enquiries.index') }}" class="menu-link">
                        <div data-i18n="Without menu">Client Enquiries</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcanany

    </ul>
</aside>
