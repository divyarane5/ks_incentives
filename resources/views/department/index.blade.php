@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Department /</span> List</h4>
        <div class="col-md-6">
            @can('department-create')
            <a href="{{ route('department.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Department</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Department</h5>
        <div class="table-responsive text-nowrap">
            <table id="department-datatable" class="table table-striped" width="100%">
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
      var table = $('#department-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('department.index') }}",
            columns: [
                {data: 'name', name: 'departments.name'},
                {data: 'created_at', name: 'departments.created_at'},
                {data: 'updated_at', name: 'departments.updated_at'},
                {data: 'action', 'sortable': false},
            ]
      });

    });

    function deleteDepartment(id)
    {
        $.confirm({
            title: 'Delete Department',
            content: 'Are you sure you want to delete department?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                tryAgain: {
                    text: 'Yes',
                    btnClass: 'btn-red',
                    action: function(){
                        event.preventDefault();
                        document.getElementById(id).submit()
                    }
                },
                close: function () {
                }
            }
        });
    }
</script>
@endsection
