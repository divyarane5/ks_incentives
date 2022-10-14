@extends('layouts.app')

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Users /</span> List</h4>
        <div class="col-md-6 ">
            <a href="{{ route('users.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add User</a>
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Users</h5>

        <div class="table-responsive text-nowrap">
            <table id="user-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Location</th>
                        <th>Company</th>
                        <th>Action</th>
                    </tr>
                </thead>
            <tbody class="table-border-bottom-0">
            </tbody>
            </table>
        </div>
    </div>
    <!--/ Striped Rows -->
</div>

<!-- / Content -->
@endsection


@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#user-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('users.index') }}",
            columns: [
                {data: 'employee_code', name: 'employee_code'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'designation', name: 'designations.name'},
                {data: 'department', name: 'department'},
                {data: 'location', name: 'location'},
                {data: 'company', name: 'entity'},
                {data: 'action', 'sortable': false},
            ]
        });
    });

</script>
@endsection
