@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6">
            <span class="text-muted fw-light">Mandate Projects /</span> List
        </h4>
        <div class="col-md-6">
            @can('mandate_projects-create')
            <a href="{{ route('mandate_projects.create') }}" 
               type="button" 
               class="btn btn-primary pull-right my-3 mb-4">
               Add Project
            </a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Mandate Projects</h5>
        <div class="table-responsive text-nowrap">
            <table id="mandate-projects-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Brand</th>
                        <th>Location</th>
                        <th>RERA Number</th>
                        <th>Property Type</th>
                        <th>Configurations</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0"></tbody>
            </table>
        </div>
    </div>
    <!--/ Striped Rows -->
</div>
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function () {
    var table = $('#mandate-projects-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('mandate_projects.index') }}",
        columns: [
            { data: 'project_name', name: 'mandate_projects.project_name' },
            { data: 'brand_name', name: 'mandate_projects.brand_name' },
            { data: 'location', name: 'mandate_projects.location' },
            { data: 'rera_number', name: 'mandate_projects.rera_number' },
            { data: 'property_type', name: 'mandate_projects.property_type' },
            { data: 'configurations', name: 'configurations', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false },
        ],
        responsive: true
    });
});

function deleteMandateProject(id)
{
    $.confirm({
        title: 'Delete Project',
        content: 'Are you sure you want to delete this project?',
        type: 'red',
        typeAnimated: true,
        buttons: {
            confirm: {
                text: 'Yes',
                btnClass: 'btn-red',
                action: function(){
                    event.preventDefault();
                    document.getElementById(id).submit();
                }
            },
            close: function () {}
        }
    });
}
</script>

@endsection
