@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Designation /</span> List</h4>
        <div class="col-md-6">
            @can('designation-create')
            <a href="{{ route('designation.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Designation</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Designation</h5>
        <div class="table-responsive text-nowrap">
            <table id="designation-datatable" class="table table-striped" width="100%">
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
      var table = $('#designation-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('designation.index') }}",
            columns: [
                {data: 'name', name: 'designations.name'},
                {data: 'created_at', name: 'designations.created_at'},
                {data: 'updated_at', name: 'designations.updated_at'},
                {data: 'action', 'sortable': false},
            ]
      });

    });

    function deleteDesignation(id)
    {
        $.confirm({
            title: 'Delete Designation',
            content: 'Are you sure you want to delete designation?',
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
