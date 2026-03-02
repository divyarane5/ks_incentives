@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6">
            <span class="text-muted fw-light">Project /</span> List
        </h4>

        <div class="col-md-6 text-end">
            @can('project-create')
                <a href="{{ route('project.create') }}" class="btn btn-primary my-3 mb-4">
                    Add Project
                </a>
            @endcan
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Projects</h5>

        <div class="table-responsive">
            <table id="project-datatable"
                   class="table table-bordered table-hover align-middle"
                   width="100%">
                <thead class="table-light">
                    <tr>
                        <th>Project Name</th>
                        <th>Developer</th>
                        <th>Ladders</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>
$(document).ready(function () {

    $('#project-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('project.index') }}",
        responsive: true,
        autoWidth: false,
        columns: [
            {data: 'name', name: 'name'},
            {data: 'developer', name: 'developer', orderable: false},
            {data: 'ladders', name: 'ladders', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

});

function deleteProject(id)
{
    $.confirm({
        title: 'Delete Project',
        content: 'Are you sure you want to delete this project?',
        type: 'red',
        buttons: {
            confirm: {
                text: 'Yes',
                btnClass: 'btn-red',
                action: function(){
                    document.getElementById('delete-' + id).submit();
                }
            },
            cancel: function () {}
        }
    });
}
</script>
@endsection