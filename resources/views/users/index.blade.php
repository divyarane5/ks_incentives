@extends('layouts.app')

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Users /</span> List</h4>
        <div class="col-md-6">
            @can('user-create')
            <a href="{{ route('users.create') }}" class="btn btn-primary pull-right my-3 mb-4">Add User</a>
            @endcan
        </div>
    </div>

    <div class="card my-4" id="UserFilter">
        <div class="card-body row">
            <div class="mb-3 col-md-3">
                <label class="form-label" for="business_unit_id">Business Unit</label>
                <select id="business_unit_id" name="business_unit_id" class="form-select">
                    <option value="">Select Business Unit</option>
                    @foreach ($businessUnits as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="department_id">Department</label>
                <select id="department_id" name="department_id" class="">
                    <option value="">Select Department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label" for="designation_id">Designation</label>
                <select id="designation_id" name="designation_id" class="">
                    <option value="">Select Designation</option>
                    @foreach ($designations as $designation)
                        <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-3">
                <label class="form-label" for="role_id">Role</label>
                <select id="role_id" name="role_id" class="">
                    <option value="">Select Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="reporting_manager_id">Reporting Manager</label>
                <select id="reporting_manager_id" name="reporting_manager_id" class="form-select">
                    <option value="">Select Manager</option>
                    @foreach ($reportingUsers as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->full_name ?? $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="employment_status">Employment Status</label>
                <select id="employment_status" name="employment_status" class="form-select">
                    <option value="">Select Employment Status</option>
                    <option value="Probation">Probation</option>
                    <option value="Confirmed">Confirmed</option>
                </select>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label" for="status">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="">Select Status</option>
                    <option value="Active">Active</option>
                    <option value="Exited">Exited</option>
                </select>
            </div>
            <div class="mb-3 col-md-3">
                <label class="form-label">&nbsp;</label><br>
                <button type="button" id="filter" class="btn btn-primary me-sm-2">Filter</button>
                <button type="button" id="clear" class="btn btn-secondary">Clear</button>
            </div>
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <div class="row">
            <h5 class="card-header col-md-6">Users</h5>
            <div class="datatableButtons mr-2 pull-right my-3 mb-4 right-align col-md-6"></div>
        </div>

        <div class="table-responsive text-nowrap">
            <table id="user-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Location</th>
                        <th>Company</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0"></tbody>
            </table>
        </div>
    </div>
</div>
<!-- / Content -->
@endsection


@section('script')
<script src="{{ asset('assets/vendor/dataTable/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/jszip.min.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/vendor/dataTable/buttons.html5.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function () {
    var table = $('#user-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('users.index') }}",
            data: function (d) {
                d.department_id = $("#department_id").val();
                d.designation_id = $("#designation_id").val();
                d.role_id = $("#role_id").val();

                d.status = $("#status").val();
                d.employment_status = $("#employment_status").val();
                d.business_unit_id = $("#business_unit_id").val();
                d.reporting_manager_id = $("#reporting_manager_id").val();
            }
        },
        columns: [
            {data: 'employee_code', name: 'users.employee_code'},
            {data: 'name', name: 'users.name'},
            {data: 'role', name: 'roles.name', sortable: false},
            {data: 'email', name: 'users.email'},
            {data: 'designation', name: 'designation', sortable: false},
            {data: 'department', name: 'department', sortable: false},
            {data: 'location', name: 'location', sortable: false},
            {data: 'company', name: 'users.entity'},
            {data: 'action', sortable: false},
        ]

    });

    $("#filter").on('click', function () {
        table.ajax.reload();
    });

    $("#clear").on('click', function () {
        $("#UserFilter select").val('');
        // Uncomment below line only if using Bootstrap Select
        // $("#UserFilter select").selectpicker('refresh');
        table.ajax.reload();
    });

    var buttons = new $.fn.dataTable.Buttons(table, {
        buttons: [{
            extend: 'excelHtml5',
            title: 'user_export',
            exportOptions: { columns: [0,1,2,3,4,5,6,7] }
        }]
    }).container().appendTo($('.datatableButtons'));

    $('.buttons-excel')
        .removeClass('dt-button buttons-excel buttons-html5')
        .addClass('btn btn-success btn-plus mx-3')
        .attr('id', 'excelHtml5')
        .html('<i class="fa fa-file-excel-o mr-2"></i> Export to Excel');
});

function deleteUser(id) {
    $.confirm({
        title: 'Delete User',
        content: 'Are you sure you want to delete this user?',
        type: 'red',
        typeAnimated: true,
        buttons: {
            confirm: {
                text: 'Yes',
                btnClass: 'btn-red',
                action: function(){
                    event.preventDefault();
                    document.getElementById('delete-form-' + id).submit();
                }
            },
            cancel: function () {}
        }
    });
}
</script>
@endsection
