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

    <div class="card my-4" id="MandateProjectFilter">
        <div class="card-body row">

            <div class="mb-3 col-md-4">
                <label class="form-label">Project Name</label>
                <select id="project_name" class="form-select">
                    <option value="">All</option>
                    @foreach($projects as $project)
                        <option value="{{ $project }}">{{ $project }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">Brand Name</label>
                <select id="brand_name" class="form-select">
                    <option value="">All</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand }}">{{ $brand }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">&nbsp;</label><br>
                <button id="filter" class="btn btn-primary me-2">Filter</button>
                <button id="clear" class="btn btn-secondary">Clear</button>
            </div>

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
                        <th>Threshold %</th>
                        <th>Brokerage %</th>
                        <th>Brokerage Criteria</th>
                        <th>Configurations</th>
                        <th>Ladders</th>
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
        ajax: {
            url: "{{ route('mandate_projects.index') }}",
            data: function (d) {
                d.project_name = $('#project_name').val();
                d.brand_name   = $('#brand_name').val();
            }
        },
        columns: [
            { data: 'project_name', name: 'mandate_projects.project_name' },
            { data: 'brand_name', name: 'mandate_projects.brand_name' },
            { data: 'location', name: 'mandate_projects.location' },
            { data: 'rera_number', name: 'mandate_projects.rera_number' },
            { data: 'property_type', name: 'mandate_projects.property_type' },
            { data: 'threshold_percentage', name: 'mandate_projects.threshold_percentage' },
            { data: 'brokerage', name: 'mandate_projects.brokerage' },
            { data: 'brokerage_criteria', name: 'mandate_projects.brokerage_criteria' },
            { data: 'configurations', name: 'configurations', orderable: false, searchable: false },
            { data: 'ladders', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false },
        ],
        responsive: true
    });
     // 🔍 APPLY FILTER
    $('#filter').on('click', function (e) {
        e.preventDefault();
        table.ajax.reload(null, false);
    });

    // 🧹 CLEAR FILTER (FIXED)
    $('#clear').on('click', function (e) {
        e.preventDefault();

        // reset dropdowns
        $('#project_name').val('');
        $('#brand_name').val('');

        // force reload from first page
        table.ajax.reload(null, true);
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
