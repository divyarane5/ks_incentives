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
    @canany(['role-view'])
        <li class="menu-item {{ in_array(Request::segment(1), ['role']) ? 'active open': '' }}">
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
        </li>
    @endcanany
    @can('user-view')
    <li class="menu-item {{ ((Request::segment(1) == 'users')||(Request::segment(1) == '')) ? 'active': '' }}">
        <a href="{{ route('users.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div data-i18n="Analytics">User</div>
        </a>
    </li>
    @endcan

    </ul>
</aside>
