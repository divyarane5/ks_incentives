@extends('layouts.app')

@section('content')
@include('users.partials.people-styles')
<style>
    .ul-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px; flex-wrap: wrap; }
    .ul-top h4 { margin: 0; }
    .ul-top .btns { display: flex; gap: 8px; flex-wrap: wrap; }
    .ul-top .btn { min-height: 42px; border-radius: 10px; font-weight: 600; }
    .ul-tiles { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 14px; }
    .ul-tiles button { text-align: left; cursor: pointer; }
    .ul-tiles button.on { outline: 2px solid var(--kp-gold); }
    .ul-tiles .v { font-size: 1.35rem !important; }
    .ul-search { display: flex; gap: 8px; margin-bottom: 10px; }
    .ul-search .inp { flex: 1; position: relative; }
    .ul-search .inp i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--kp-muted); font-size: 1.2rem; }
    .ul-search input { min-height: 46px; border-radius: 12px; padding-left: 38px; font-size: 16px; }
    .ul-search .btn { min-height: 46px; border-radius: 12px; font-weight: 600; white-space: nowrap; }
    .ul-filters { display: none; }
    .ul-filters.open { display: block; }
    .ul-filters .form-label { font-size: .75rem; font-weight: 600; color: var(--kp-muted); margin-bottom: 3px; }
    .ul-filters .form-select { min-height: 42px; border-radius: 10px; }

    .ul-person { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .ul-av { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 2px solid var(--kp-line); }
    .ul-ini { display: grid; place-items: center; background: var(--kp-navy); color: #fff; font-weight: 700; font-size: .9rem; border-color: var(--kp-gold); }
    .ul-txt { min-width: 0; flex: 1; }
    .ul-name { font-weight: 700; color: var(--kp-navy) !important; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ul-sub, .ul-meta { font-size: .8rem; color: var(--kp-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ul-chips { margin-top: 4px; display: flex; gap: 4px; flex-wrap: wrap; }
    .ul-call { width: 40px; height: 40px; border-radius: 50%; background: var(--rs-navy-soft, #eef1f6); color: var(--kp-navy) !important; display: grid; place-items: center; font-size: 1.2rem; flex-shrink: 0; }

    #user-datatable { margin: 0 !important; }
    #user-datatable td { vertical-align: middle; white-space: normal; }
    #user-datatable td.ul-col-person { min-width: 260px; }
    .kp-panel .dataTables_wrapper .row { margin: 0; padding: 8px 12px; align-items: center; }
    .kp-panel .dataTables_filter { display: none; }
    .kp-panel .dataTables_length select { min-height: 36px; border-radius: 8px; }
    .kp-panel .dataTables_paginate .pagination { flex-wrap: wrap; justify-content: center; margin: 0; }
    .kp-panel .dataTables_info { font-size: .8rem; color: var(--kp-muted); }

    /* Phones: each person is a card; the other columns are for desktop and Excel. */
    @media (max-width: 767.98px) {
        #user-datatable thead { display: none; }
        #user-datatable, #user-datatable tbody, #user-datatable tr { display: block; width: 100%; }
        #user-datatable tr { position: relative; border-bottom: 1px solid var(--kp-line); background: #fff !important; padding: 12px 44px 12px 14px; box-shadow: none !important; }
        #user-datatable td { display: none; border: 0 !important; padding: 0 !important; background: transparent !important; box-shadow: none !important; }
        #user-datatable td.ul-col-person { display: block; min-width: 0; }
        #user-datatable td.ul-col-action { display: block; position: absolute; right: 8px; top: 14px; }
        #user-datatable td.dataTables_empty { display: block; text-align: center; padding: 20px 0 !important; }
        .table-responsive { overflow: visible; }
        .kp-panel .dataTables_wrapper .row > div { width: 100%; text-align: center; }
        .kp-panel .dataTables_length { margin-bottom: 4px; }
    }
    @media (min-width: 768px) {
        .ul-tiles { grid-template-columns: repeat(4, 1fr); }
        .ul-call { display: none; }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y kp" style="max-width:1400px">
    <div class="kp-crumb"><a href="{{ route('users.index') }}">KeyPeople</a> <i class="bx bx-chevron-right"></i> <span>Employees</span></div>

    <div class="ul-top">
        <h4>Employees</h4>
        <div class="btns">
            <span class="datatableButtons"></span>
            @can('user-create')
                <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bx bx-user-plus"></i> Add employee</a>
            @endcan
        </div>
    </div>

    <div class="ul-tiles">
        <button type="button" class="kp-fact" data-quick="active"><div class="l">Active</div><div class="v">{{ $counts['active'] }}</div><div class="s">people working now</div></button>
        <button type="button" class="kp-fact" data-quick="probation"><div class="l">On probation</div><div class="v">{{ $counts['probation'] }}</div><div class="s">not yet confirmed</div></button>
        <div class="kp-fact"><div class="l">New joiners</div><div class="v">{{ $counts['joined'] }}</div><div class="s">this and last 2 months</div></div>
        <button type="button" class="kp-fact" data-quick="exited"><div class="l">Exited</div><div class="v">{{ $counts['exited'] }}</div><div class="s">have left{{ $counts['inactive'] ? ' · '.$counts['inactive'].' inactive' : '' }}</div></button>
    </div>

    <div class="ul-search">
        <div class="inp">
            <i class="bx bx-search"></i>
            <input type="search" id="ulSearch" class="form-control" placeholder="Search name, code or email" autocomplete="off">
        </div>
        <button type="button" class="btn btn-outline-primary" id="ulFilterBtn"><i class="bx bx-filter-alt"></i> Filters <span class="badge bg-primary ms-1" id="ulFilterCount" style="display:none">0</span></button>
    </div>

    <div class="kp-panel ul-filters" id="UserFilter">
        <div class="kp-panel-b row g-3" style="padding:14px">
            <div class="col-6 col-md-3">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select raw-select">
                    <option value="">Everyone</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Exited">Exited</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label" for="employment_status">Probation / confirmed</label>
                <select id="employment_status" name="employment_status" class="form-select raw-select">
                    <option value="">Any</option>
                    <option value="Probation">Probation</option>
                    <option value="Confirmed">Confirmed</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label" for="business_unit_id">Business unit</label>
                <select id="business_unit_id" name="business_unit_id" class="form-select raw-select">
                    <option value="">Any</option>
                    @foreach ($businessUnits as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label" for="department_id">Department</label>
                <select id="department_id" name="department_id" class="form-select">
                    <option value="">Any</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label" for="designation_id">Designation</label>
                <select id="designation_id" name="designation_id" class="form-select">
                    <option value="">Any</option>
                    @foreach ($designations as $designation)
                        <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label" for="role_id">Role</label>
                <select id="role_id" name="role_id" class="form-select">
                    <option value="">Any</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label" for="reporting_manager_id">Reports to</label>
                <select id="reporting_manager_id" name="reporting_manager_id" class="form-select">
                    <option value="">Anyone</option>
                    @foreach ($reportingUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}{{ $user->employee_code ? ' ('.$user->employee_code.')' : '' }}{{ $user->status !== 'Active' ? ' - '.$user->status : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                <button type="button" id="filter" class="btn btn-primary flex-fill" style="min-height:42px">Apply</button>
                <button type="button" id="clear" class="btn btn-outline-secondary flex-fill" style="min-height:42px">Clear</button>
            </div>
        </div>
    </div>

    <div class="kp-panel">
        <div class="table-responsive">
            <table id="user-datatable" class="table" width="100%">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Location</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/vendor/dataTable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/jszip.min.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/buttons.html5.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    var filterIds = ['status', 'employment_status', 'business_unit_id', 'department_id', 'designation_id', 'role_id', 'reporting_manager_id'];
    try { var saved = JSON.parse(localStorage.getItem('kpUserFilters') || '{}'); filterIds.forEach(function (id) { if (saved[id]) $('#' + id).val(saved[id]); }); } catch (e) {}
    if (!$('#status').val() && !localStorage.getItem('kpUserFilters')) $('#status').val('Active');

    var table = $('#user-datatable').DataTable({
        processing: true,
        serverSide: true,
        dom: 'rt<"row"<"col-md-4"l><"col-md-4"i><"col-md-4"p>>',
        ajax: {
            url: "{{ route('users.index') }}",
            data: function (d) { filterIds.forEach(function (id) { d[id] = $('#' + id).val(); }); }
        },
        columns: [
            {data: 'person', name: 'users.name', className: 'ul-col-person', searchable: false},
            {data: 'employee_code', name: 'users.employee_code'},
            {data: 'name', name: 'users.name', visible: false},
            {data: 'role', name: 'roles.name', sortable: false, searchable: false},
            {data: 'email', name: 'users.email'},
            {data: 'designation', name: 'designations.name', sortable: false},
            {data: 'department', name: 'departments.name', sortable: false},
            {data: 'location', name: 'locations.name', sortable: false},
            {data: 'company', name: 'users.entity'},
            {data: 'status_badge', name: 'users.status', searchable: false},
            {data: 'action', className: 'ul-col-action text-end', sortable: false, searchable: false},
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        language: { lengthMenu: 'Show _MENU_', info: '_START_–_END_ of _TOTAL_', infoEmpty: 'Nobody found', zeroRecords: 'Nobody matches. Try clearing the filters.', processing: 'Loading…' }
    });

    var t;
    $('#ulSearch').on('input', function () { var v = this.value; clearTimeout(t); t = setTimeout(function () { table.search(v).draw(); }, 300); });

    function countFilters() {
        var n = filterIds.filter(function (id) { return $('#' + id).val(); }).length;
        $('#ulFilterCount').text(n).toggle(n > 0);
        var o = {}; filterIds.forEach(function (id) { o[id] = $('#' + id).val(); });
        try { localStorage.setItem('kpUserFilters', JSON.stringify(o)); } catch (e) {}
        var st = $('#status').val(), em = $('#employment_status').val();
        $('[data-quick]').removeClass('on');
        if (st === 'Active' && em === 'Probation') $('[data-quick="probation"]').addClass('on');
        else if (st === 'Active' && !em) $('[data-quick="active"]').addClass('on');
        else if (st === 'Exited') $('[data-quick="exited"]').addClass('on');
    }
    countFilters();

    $('#ulFilterBtn').on('click', function () { $('#UserFilter').toggleClass('open'); });
    $('#filter').on('click', function () { countFilters(); table.ajax.reload(); if (window.innerWidth < 768) $('#UserFilter').removeClass('open'); });
    $('#UserFilter select.raw-select').on('change', function () { countFilters(); table.ajax.reload(); });
    $('#clear').on('click', function () {
        $('#UserFilter select').val('');
        $('#UserFilter select:not(.raw-select)').selectpicker('refresh');
        countFilters(); table.ajax.reload();
    });
    $('[data-quick]').on('click', function () {
        var q = $(this).data('quick');
        $('#status').val(q === 'exited' ? 'Exited' : 'Active');
        $('#employment_status').val(q === 'probation' ? 'Probation' : '');
        countFilters(); table.ajax.reload();
    });

    new $.fn.dataTable.Buttons(table, {
        buttons: [{ extend: 'excelHtml5', title: 'employees', exportOptions: { columns: [1,2,3,4,5,6,7,8,9] } }]
    }).container().appendTo($('.datatableButtons'));
    $('.buttons-excel')
        .removeClass('dt-button buttons-excel buttons-html5')
        .addClass('btn btn-outline-success')
        .html('<i class="bx bx-download"></i> Excel');
});

function deleteUser(id) {
    $.confirm({
        title: 'Delete employee',
        content: 'Are you sure you want to delete this employee?',
        type: 'red',
        typeAnimated: true,
        buttons: {
            confirm: { text: 'Yes', btnClass: 'btn-red', action: function () { document.getElementById('delete-form-' + id).submit(); } },
            cancel: function () {}
        }
    });
}
</script>
@endsection
