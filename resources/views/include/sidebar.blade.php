<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link">
        <img src="{{ asset("assets/img/logo/logo_sidebar.png") }}" >
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
    @canany(['role-view','location-view','department-view','designation-view','expense-view','vendor-view','business_unit-view','payment_method-view','template-view'])
        <li class="menu-item {{ in_array(Request::segment(1), ['role','location','department','designation','expense','vendor','business_unit','payment_method','template']) ? 'active open': '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle ">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Masters</div>
            </a>
            <ul class="menu-sub ">
                @can('role-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'role')) ? 'active': '' }}">
                        <a href="{{ route('role.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Role</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('location-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'location')) ? 'active': '' }}">
                        <a href="{{ route('location.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Location</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('department-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'department')) ? 'active': '' }}">
                        <a href="{{ route('department.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Department</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('designation-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'designation')) ? 'active': '' }}">
                        <a href="{{ route('designation.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Designation</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('vendor-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'vendor')) ? 'active': '' }}">
                        <a href="{{ route('vendor.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Vendor</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('expense-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'expense')) ? 'active': '' }}">
                        <a href="{{ route('expense.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Expense</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('business_unit-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'business_unit')) ? 'active': '' }}">
                        <a href="{{ route('business_unit.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Business Unit</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('payment_method-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'payment_method')) ? 'active': '' }}">
                        <a href="{{ route('payment_method.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Payment Method</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('referral-template-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'template')) ? 'active': '' }}">
                        <a href="{{ route('template.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Template</div>
                        </a>
                    </li>
                @endcan
            </ul>

        </li>
    @endcanany
    @can('user-view')
    <li class="menu-item {{ (Request::segment(1) == 'users') ? 'active': '' }}">
        <a href="{{ route('users.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div data-i18n="Analytics">User</div>
        </a>
    </li>
    @endcan

    </ul>
</aside>
