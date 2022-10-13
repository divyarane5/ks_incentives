@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Roles /</span> List</h4>
    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Roles</h5>
        <div class="table-responsive text-nowrap">
            <table id="role-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Created On</th>
                    <th>Modified On</th>
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
      var table = $('#role-datatable').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('role.index') }}",
          columns: [
              {data: 'name', name: 'name'},
              {data: 'created_at', name: 'created_at'},
              {data: 'updated_at', name: 'updated_at'},
              {data: 'action', 'sortable': false},
          ]
      });

    });
</script>
@endsection
