<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link">
        <img src="{{ asset('assets/img/logo/ks-logos.webp') }}" >
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
    @canany(['role-view','location-view','department-view','designation-view','expense-view','vendor-view','business_unit-view','payment_method-view'])
        <li class="menu-item {{ in_array(Request::segment(1), ['role','location','department','designation','expense','vendor','business_unit','payment_method']) ? 'active open': '' }}">
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
            
            <!-- <ul class="menu-sub ">
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
            </ul> -->
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
                @can('project-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'project')) ? 'active': '' }}">
                        <a href="{{ route('project.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Project</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('developer-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'developer')) ? 'active': '' }}">
                        <a href="{{ route('developer.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Developer</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <!-- <ul class="menu-sub ">
                @can('payment_method-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'payment_method')) ? 'active': '' }}">
                        <a href="{{ route('payment_method.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Payment Method</div>
                        </a>
                    </li>
                @endcan
            </ul> -->

        </li>
    @endcanany
    @canany(['developer-view','project-view','developer_ladder-view'])
        <li class="menu-item {{ in_array(Request::segment(1), ['role','location','department','designation','expense','vendor','business_unit','payment_method']) ? 'active open': '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle ">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Ladders</div>
            </a>
            
            
            <ul class="menu-sub ">
                @can('developer-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'developer')) ? 'active': '' }}">
                        <a href="{{ route('developer.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Developer</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('develop_ladder-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'developer_ladder')) ? 'active': '' }}">
                        <a href="{{ route('developer_ladder.index') }}" class="menu-link">
                            <div data-i18n="Without menu">AOP Ladder</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('project-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'project')) ? 'active': '' }}">
                        <a href="{{ route('project.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Project</div>
                        </a>
                    </li>
                @endcan
            </ul>
            <ul class="menu-sub ">
                @can('project_ladder-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'project_ladder')) ? 'active': '' }}">
                        <a href="{{ route('project_ladder.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Project Ladder</div>
                        </a>
                    </li>
                @endcan
            </ul>
           

        </li>
    @endcanany

    @canany(['configuration-view', 'indent-view-all', 'indent-view-own', 'indent-approval', 'indent-payment-conclude'])
        <!-- <li class="menu-item {{ in_array(Request::segment(1), ['indent_configuration', 'indent', 'indent-approval', 'indent-closure']) ? 'active open': '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle ">
                <i class="menu-icon tf-icons bx bx-dollar-circle"></i>
                <div data-i18n="Layouts">Indent</div>
            </a>
            <ul class="menu-sub">
                @can('configuration-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'indent_configuration')) ? 'active': '' }}">
                        <a href="{{ route('indent_configuration.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Indent Configuration</div>
                        </a>
                    </li>
                @endcan
                @canany(['indent-view-all', 'indent-view-own'])
                    <li class="menu-item {{ ((Request::segment(1) == 'indent')) ? 'active': '' }}">
                        <a href="{{ route('indent.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Indents</div>
                        </a>
                    </li>
                @endcan
                @can('indent-approval')
                <li class="menu-item {{ ((Request::segment(1) == 'indent-approval')) ? 'active': '' }}">
                    <a href="{{ route('indent.approval') }}" class="menu-link">
                        <div data-i18n="Without menu">Indent Approval</div>
                    </a>
                </li>
                @endcan
                @can('indent-payment-conclude')
                <li class="menu-item {{ ((Request::segment(1) == 'indent-closure')) ? 'active': '' }}">
                    <a href="{{ route('indent.closure') }}" class="menu-link">
                        <div data-i18n="Without menu">Indent Closure</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li> -->
    @endcanany

    @canany(['reimbursement-view-all', 'reimbursement-view-own', 'reimbursement-approval'])
        <!-- <li class="menu-item {{ in_array(Request::segment(1), ['reimbursement']) ? 'active open': '' }}">
            <a href="{{ route('reimbursement.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dollar-circle"></i>
                <div data-i18n="Layouts">Reimbursement</div>
            </a>
        </li> -->
    @endcanany
    @canany('booking-view')

    <li class="menu-item {{ (Request::segment(1) == 'booking') ? 'active': '' }}">
        <a href="{{ route('booking.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-layout"></i>
            <div data-i18n="Analytics">Booking</div>
        </a>
    </li>
    @endcan
    @canany(['template-view','referral-client-view','booking-view'])
        <!-- <li class="menu-item {{ in_array(Request::segment(1), ['template', 'client', 'client_response', 'client_response_service', 'booking']) ? 'active open': '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle ">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">After Sales</div>
            </a>
            <ul class="menu-sub ">
                @can('referral-template-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'template')) ? 'active': '' }}">
                        <a href="{{ route('template.index') }}" class="menu-link">
                            <div data-i18n="Without menu">Templates</div>
                        </a>
                    </li>
                @endcan
                @can('referral-client-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'client')) ? 'active': '' }}">
                        <a href="{{ route('client.index') }}" class="menu-link">
                            <div data-i18n="Without menu"> Referral Clients</div>
                        </a>
                    </li>
                @endcan
                @can('response-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'client_response')) ? 'active': '' }}">
                        <a href="{{ route('client_response.index') }}" class="menu-link">
                            <div data-i18n="Without menu"> Referral Response</div>
                        </a>
                    </li>
                @endcan
                @can('response-view')
                    <li class="menu-item {{ ((Request::segment(1) == 'client_response_service')) ? 'active': '' }}">
                        <a href="{{ url('/client_response_service') }}" class="menu-link">
                            <div data-i18n="Without menu"> Service Response</div>
                        </a>
                    </li>
                @endcan
                @can('booking-view')
                <li class="menu-item {{ (Request::segment(1) == 'booking') ? 'active': '' }}">
                    <a href="{{ route('booking.index') }}" class="menu-link">
                        
                        <div data-i18n="Analytics">Booking</div>
                    </a>
                </li>
                @endcan

            </ul>

        </li> -->
    @endcanany
  
    @can('user-view')
    <li class="menu-item {{ (Request::segment(1) == 'users') ? 'active': '' }}">
        <a href="{{ route('users.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div data-i18n="Analytics">User</div>
        </a>
    </li>
    @endcan

    @canany(['indent-view-all', 'indent-view-own', 'indent-approval', 'indent-payment-conclude', 'reimbursement-view-all', 'reimbursement-view-own', 'reimbursement-approval'])
    <!-- <li class="menu-item {{ in_array(Request::segment(1), ['indent_payments', 'reimbursement_payments']) ? 'active open': '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle ">
            <i class="menu-icon tf-icons bx bxs-report"></i>
            <div data-i18n="Layouts">Reports</div>
        </a>
        <ul class="menu-sub ">
            @canany(['indent-view-all', 'indent-view-own', 'indent-approval', 'indent-payment-conclude'])
            <li class="menu-item {{ ((Request::segment(1) == 'indent_payments')) ? 'active': '' }}">
                <a href="{{ route('reports.indent_payments') }}" class="menu-link">
                    <div data-i18n="Without menu">Indent Payments</div>
                </a>
            </li>
            @endcanany
            @canany(['reimbursement-view-all', 'reimbursement-view-own', 'reimbursement-approval'])
            <li class="menu-item {{ ((Request::segment(1) == 'reimbursement_payments')) ? 'active': '' }}">
                <a href="{{ route('reports.reimbursement_payments') }}" class="menu-link">
                    <div data-i18n="Without menu">Reimbursement Payments</div>
                </a>
            </li>
            @endcanany
        </ul>
    </li> -->
    @endcanany

    @can('candidate-view')
    <li class="menu-item {{ (Request::segment(1) == 'candidate') ? 'active': '' }}">
        <a href="{{ route('candidate.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div data-i18n="Analytics">Candidate</div>
        </a>
    </li>
    @endcan
    </ul>
</aside>
