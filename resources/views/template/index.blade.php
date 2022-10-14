@extends('layouts.app')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Users /</span> List</h4>
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            </tbody>
            </table>
        </div>
        </div>
        <!--/ Striped Rows -->
    </div>
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
              {data: 'designation', name: 'designation'},
              {data: 'department', name: 'department'},
              {data: 'location', name: 'location'},
              {data: 'entity', name: 'entity'},
              {data: 'action', 'sortable': false},
          ]
      });

    });
</script>
@endsection
