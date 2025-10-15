@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Project /</span> List</h4>
        <div class="col-md-6">
            @can('project-create')
            <a href="{{ route('project.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Project</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Projects</h5>
        <div class="table-responsive text-nowrap">
            <table id="project-datatable" class="table table-striped" width="100%">
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
      var table = $('#project-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('project.index') }}",
            columns: [
                {data: 'name', name: 'projects.name'},
                {data: 'created_at', name: 'projects.created_at'},
                {data: 'updated_at', name: 'projects.updated_at'},
                {data: 'action', 'sortable': false},
            ]
      });

    });

    function deleteLocation(id)
    {
        $.confirm({
            title: 'Delete Location',
            content: 'Are you sure you want to delete location?',
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
