<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <img src="{{ asset('assets/img/logo/realtysuite-white.svg') }}" alt="RealtySuite" class="img-fluid">
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
                <div>Home</div>
            </a>
        </li>

        @php
            $isSAorAdmin = isSuperAdmin() || isAdmin();
            $krea = $isSAorAdmin || auth()->user()->businessUnit?->code === 'KREA';
        @endphp

        {{-- ================= KeyLeads ================= --}}
        @if(auth()->user()?->usesCrm())
        <li class="menu-header small text-uppercase"><span class="menu-header-text">KeyLeads</span></li>
        <li class="menu-item">
            <a href="{{ route('crm.dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-phone-call"></i>
                <div>Leads</div>
            </a>
        </li>
        @endif

        {{-- ================= KeyBookings ================= --}}
        @can('module-keybookings')
        <li class="menu-header small text-uppercase"><span class="menu-header-text">KeyBookings</span></li>
        @if($krea)
            @can('booking-view')
            <li class="menu-item {{ Request::segment(1) == 'booking' ? 'active' : '' }}">
                <a href="{{ route('booking.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-book"></i>
                    <div>Bookings</div>
                </a>
            </li>
            @endcan
            @can('project-view')
            <li class="menu-item {{ Request::segment(1) == 'project' ? 'active' : '' }}">
                <a href="{{ route('project.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-buildings"></i>
                    <div>Projects</div>
                </a>
            </li>
            @endcan
            @can('developer-view')
            <li class="menu-item {{ Request::segment(1) == 'developer' ? 'active' : '' }}">
                <a href="{{ route('developer.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-briefcase"></i>
                    <div>Developers</div>
                </a>
            </li>
            @endcan
        @endif
        {{-- Mandate Section (unchanged) --}}
        @if($isSAorAdmin || auth()->user()->businessUnit?->code === 'AI')
            <li class="menu-item {{ in_array(Request::segment(1), ['mandate_projects','channel_partners','client-enquiries']) ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-briefcase"></i>
                    <div>Mandate</div>
                </a>

                <ul class="menu-sub">

                    @if($isSAorAdmin || canAccessModule('mandate_project-view', ['AI']))
                        <li class="menu-item {{ Request::segment(1) == 'mandate_projects' ? 'active' : '' }}">
                            <a href="{{ route('mandate_projects.index') }}" class="menu-link">
                                <div>Mandate Projects</div>
                            </a>
                        </li>
                    @endif

                    @if($isSAorAdmin || canAccessModule('channel-partner-view', ['AI']))
                        <li class="menu-item {{ Request::segment(1) == 'channel_partners' ? 'active' : '' }}">
                            <a href="{{ route('channel_partners.index') }}" class="menu-link">
                                <div>Channel Partner</div>
                            </a>
                        </li>
                    @endif

                    @if($isSAorAdmin || canAccessModule('client-enquiry-view', ['AI']))
                        <li class="menu-item {{ Request::segment(1) == 'client-enquiries' ? 'active' : '' }}">
                            <a href="{{ route('client-enquiries.index') }}" class="menu-link">
                                <div>Client Enquiries</div>
                            </a>
                        </li>
                    @endif

                    @if(canAccessModule('mandate-booking-view', ['AI']))
                        <li class="menu-item {{ Request::segment(1) == 'mandate_bookings' ? 'active' : '' }}">
                            <a href="{{ route('mandate_bookings.index') }}" class="menu-link">
                                <div>Mandate Bookings</div>
                            </a>
                        </li>
                    @endif



                </ul>
            </li>
        @endif
        @endcan

        {{-- ================= KeyAccounts ================= --}}
        @can('module-keyaccounts')
        <li class="menu-header small text-uppercase"><span class="menu-header-text">KeyAccounts</span></li>
        @if($krea)
            @can('payment-view')
            <li class="menu-item">
                <a href="{{ route('brokerage-payments.index') }}"
                class="menu-link">

                <i class="menu-icon tf-icons bx bx-money"></i>

                <div>Brokerage Payments</div>
                </a>
            </li>
            @endcan
            @can('company-bank-view')
            <li class="menu-item {{ Request::segment(1) == 'company-bank' ? 'active' : '' }}">
                <a href="{{ route('company-bank.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-bank"></i>
                    <div>Company Bank Accounts</div>
                </a>
            </li>
            @endcan
        @endif
                    @can('mandate-booking-view')
                        <li class="menu-item {{ Request::segment(1) == 'brokerage-ledgers' ? 'active' : '' }}">
                            <a href="{{ url('/brokerage-ledgers') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                                <div>Brokerage Ledgers</div>
                            </a>
                        </li>
                    @endcan
        @endcan

        {{-- ================= KeyMe (everyone: own leave, payslips, letters) ================= --}}
        @php
            $teamLeavePending = \App\Models\LeaveRequest::where('approver_id', auth()->id())->where('status', 'pending')->count();
            $isManager = $teamLeavePending > 0 || \App\Models\User::where('reporting_manager_id', auth()->id())->where('status', 'Active')->exists();
        @endphp
        <li class="menu-header small text-uppercase"><span class="menu-header-text">KeyMe</span></li>
        <li class="menu-item {{ Request::is('me') ? 'active' : '' }}">
            <a href="{{ route('me.dashboard') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-heart"></i><div>My dashboard</div></a>
        </li>
        <li class="menu-item {{ Request::is('me/leave*') ? 'active' : '' }}">
            <a href="{{ route('leave.my') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-calendar-event"></i><div>My leave</div></a>
        </li>
        @if($isManager)
        <li class="menu-item {{ Request::is('me/team-leave*') ? 'active' : '' }}">
            <a href="{{ route('leave.team') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-calendar-check"></i><div>Team leave</div>
                @if($teamLeavePending)<span class="badge rounded-pill bg-danger ms-auto">{{ $teamLeavePending }}</span>@endif
            </a>
        </li>
        @endif
        <li class="menu-item {{ Request::is('me/payslips*') ? 'active' : '' }}">
            <a href="{{ route('payslip.mine') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-receipt"></i><div>My payslips</div></a>
        </li>
        <li class="menu-item {{ Request::is('me/letters*') ? 'active' : '' }}">
            <a href="{{ route('letters.mine') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-envelope"></i><div>My letters</div></a>
        </li>

        {{-- ================= KeyPeople ================= --}}
        @can('module-keypeople')
        <li class="menu-header small text-uppercase"><span class="menu-header-text">KeyPeople</span></li>
        @if(\App\Services\LeaveService::canSeeHrDashboard(auth()->user()))
        <li class="menu-item {{ Request::is('hr/dashboard') ? 'active' : '' }}">
            <a href="{{ route('hr.dashboard') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-grid-alt"></i><div>HR dashboard</div></a>
        </li>
        @endif
        @can('user-view')
        <li class="menu-item {{ (Request::segment(1) == 'users') ? 'active': '' }}">
            <a href="{{ route('users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>User</div>
            </a>
        </li>
        @endcan

        @can('user-edit')
        <li class="menu-item {{ Request::is('hr/leave*') ? 'active' : '' }}">
            <a href="{{ route('leave.hr') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-calendar"></i><div>Leave</div>
                @php $hrLeavePending = \App\Models\LeaveRequest::where('status', 'pending')->count(); @endphp
                @if($hrLeavePending)<span class="badge rounded-pill bg-label-warning ms-auto">{{ $hrLeavePending }}</span>@endif
            </a>
        </li>
        <li class="menu-item {{ Request::is('hr/letter*') ? 'active' : '' }}">
            <a href="{{ route('letters.index') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-file"></i><div>Letters</div></a>
        </li>
        <li class="menu-item {{ Request::is('hr/celebrations*') ? 'active' : '' }}">
            <a href="{{ route('celebrations.index') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-cake"></i><div>Celebrations</div></a>
        </li>
        @endcan
        @can('user-salary-view')
        <li class="menu-item {{ Request::is('hr/salary-register*') ? 'active' : '' }}">
            <a href="{{ route('payslip.register') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-spreadsheet"></i><div>Salary register</div></a>
        </li>
        @endcan

        <!-- Masters -->
        @canany(['role-view','location-view','department-view','designation-view','expense-view','vendor-view','business_unit-view','payment_method-view'])
        <li class="menu-item {{ in_array(Request::segment(1), ['role','location','department','designation','expense','vendor','business_unit','payment_method']) ? 'active open': '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div>Masters</div>
            </a>

            @can('role-view')
            <ul class="menu-sub">
                <li class="menu-item {{ Request::segment(1) == 'role' ? 'active': '' }}">
                    <a href="{{ route('role.index') }}" class="menu-link">
                        <div>Role</div>
                    </a>
                </li>
            </ul>
            @endcan

            @can('location-view')
            <ul class="menu-sub">
                <li class="menu-item {{ Request::segment(1) == 'location' ? 'active': '' }}">
                    <a href="{{ route('location.index') }}" class="menu-link">
                        <div>Location</div>
                    </a>
                </li>
            </ul>
            @endcan

            @can('department-view')
            <ul class="menu-sub">
                <li class="menu-item {{ Request::segment(1) == 'department' ? 'active': '' }}">
                    <a href="{{ route('department.index') }}" class="menu-link">
                        <div>Department</div>
                    </a>
                </li>
            </ul>
            @endcan

            @can('designation-view')
            <ul class="menu-sub">
                <li class="menu-item {{ Request::segment(1) == 'designation' ? 'active': '' }}">
                    <a href="{{ route('designation.index') }}" class="menu-link">
                        <div>Designation</div>
                    </a>
                </li>
            </ul>
            @endcan

            @can('business_unit-view')
            <ul class="menu-sub">
                <li class="menu-item {{ Request::segment(1) == 'business_unit' ? 'active': '' }}">
                    <a href="{{ route('business_unit.index') }}" class="menu-link">
                        <div>Business Unit</div>
                    </a>
                </li>
            </ul>
            @endcan
        </li>
        @endcanany

        @if($isSAorAdmin)
        <li class="menu-item {{ Request::segment(1) == 'module-access' ? 'active' : '' }}">
            <a href="{{ route('module-access.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-lock-open"></i>
                <div>Module access</div>
            </a>
        </li>
        @endif
        @endcan

        {{-- ================= KeyEarn ================= --}}
        @can('module-keyearn')
        <li class="menu-header small text-uppercase"><span class="menu-header-text">KeyEarn</span></li>
        <li class="menu-item {{ Request::segment(1) == 'my-incentive' ? 'active' : '' }}">
            <a href="{{ route('my-incentive') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-wallet"></i>
                <div>My incentive</div>
            </a>
        </li>
        {{-- Incentives --}}
        @canany([
            'incentive-slab-view',
            'incentive-calculation-view',
            'incentive-hierarchy-view',
            'incentive-ledger-view'
        ])

        <li class="menu-item {{ in_array(Request::segment(1), ['incentives', 'incentive-slabs', 'incentive-ledger']) ? 'active open' : '' }}">

            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-rupee"></i>
                <div>Incentives</div>
            </a>

            <ul class="menu-sub">

                {{-- Incentive Slabs --}}
                @can('incentive-slab-view')
                <li class="menu-item {{ Request::segment(1) == 'incentive-slabs' ? 'active' : '' }}">
                    <a href="{{ route('incentive-slabs.index') }}" class="menu-link">
                        <div>Slabs</div>
                    </a>
                </li>
                @endcan

                {{-- Incentive Calculations --}}
                @can('incentive-calculation-view')
                <li class="menu-item {{ Request::segment(2) == 'preview' ? 'active' : '' }}">
                    <a href="{{ route('incentives.preview') }}" class="menu-link">
                        <div>Calculations</div>
                    </a>
                </li>
                @endcan

                {{-- Ledger --}}
                @can('incentive-ledger-view')
                <li class="menu-item {{ Request::segment(1) == 'incentive-ledger' ? 'active' : '' }}">
                    <a href="{{ route('incentive-ledger.index') }}" class="menu-link">
                        <div>Ledger & Freeze</div>
                    </a>
                </li>
                @endcan

            </ul>
        </li>

        @endcanany
        @endcan

    </ul>
</aside>
