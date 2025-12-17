@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Client Enquiry /</span> List</h4>
        <div class="col-md-6">
            @can('client-enquiries-create')
            <a href="{{ route('client-enquiries.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4">Add Enquiry</a>
            @endcan
        </div>
    </div>
    <div class="card my-4" id="EnquiryFilter">
        <div class="card-body row">
            <!-- Project -->
            <div class="mb-3 col-md-3">
                <label class="form-label">Project</label>
                <select id="project_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Source of Visit -->
            <div class="mb-3 col-md-3">
                <label class="form-label">Source of Visit</label>
                <select id="source_of_visit" class="form-select">
                    <option value="">All</option>
                    @foreach ($sources as $source)
                        <option value="{{ $source }}">{{ $source }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Channel Partner -->
            <div class="mb-3 col-md-3">
                <label class="form-label">Channel Partner</label>
                <select id="channel_partner_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($channelPartners as $cp)
                        <option value="{{ $cp->id }}">{{ $cp->firm_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sourcing Manager -->
            <div class="mb-3 col-md-3">
                <label class="form-label">Sourcing Manager</label>
                <select id="sourcing_manager_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Closing Manager -->
            <div class="mb-3 col-md-3">
                <label class="form-label">Closing Manager</label>
                <select id="closing_manager_id" class="form-select">
                    <option value="">All</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="mb-3 col-md-3">
                <label class="form-label">&nbsp;</label><br>
                <button id="filter" class="btn btn-primary me-2">Filter</button>
                <button id="clear" class="btn btn-secondary">Clear</button>
            </div>

        </div>
    </div>


    <div class="card">
        <h5 class="card-header">Client Enquiries</h5>
        <div class="table-responsive text-nowrap">
            <table id="client-enquiry-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Project Name</th>
                        <th>Customer Name</th>
                        <th>Contact No</th>
                        <th>Property Type</th>
                        <th>Purchase Purpose</th>
                        <th>Funding Source</th>
                        <th>Source of Visit</th>
                        <th>Channel Partner</th>
                        <th>Sourcing Manager</th>
                        <th>Closing Manager</th>
                        <th>Created At</th>
                        <th>Initial Feedback</th>
                        <th>Latest Feedback</th>
                        <th>Feedback History</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#client-enquiry-datatable').DataTable({
            processing: true,
            serverSide: true,
             ajax: {
                url: "{{ route('client-enquiries.index') }}",
                data: function (d) {
                    d.mandate_project_id = $('#project_id').val();
                    d.source_of_visit = $('#source_of_visit').val();
                    d.channel_partner_id = $('#channel_partner_id').val();
                    d.sourcing_manager_id = $('#sourcing_manager_id').val();
                    d.closing_manager_id = $('#closing_manager_id').val();
                }
            },
            columns: [
                {data: 'id', name: 'client_enquiries.id'},
                { data: 'project_name', name: 'mandate_projects.project_name' },
                {data: 'customer_name', name: 'client_enquiries.customer_name'},
                {data: 'contact_no', name: 'client_enquiries.contact_no'},
                {data: 'property_type', name: 'client_enquiries.property_type'},
                {data: 'purchase_purpose', name: 'client_enquiries.purchase_purpose'},
                {data: 'funding_source', name: 'client_enquiries.funding_source'},
                {data: 'source_of_visit', name: 'client_enquiries.source_of_visit'},
                {data: 'channel_partner', name: 'channel_partner.firm_name'},
                {data: 'sourcing_manager', name: 'sourcing_manager.name'},
                {data: 'closing_manager', name: 'closing_manager.name'},
                {data: 'created_at', name: 'client_enquiries.created_at'},
                {data: 'feedback', name: 'client_enquiries.feedback'},
                {data: 'latest_update', name: 'latest_update', orderable: false, searchable: false},
                {data: 'history', orderable: false, searchable: false},   // 👈 NEW
                {data: 'action', orderable: false, searchable: false},
            ]
        });

        $('#filter').click(function () {
            table.ajax.reload();
        });

        $('#clear').click(function () {
            $('#EnquiryFilter select').val('');
            table.ajax.reload();
        });
    });
    
    function deleteClientEnquiry(id) {
    $.confirm({
        title: 'Delete Enquiry',
        content: 'Are you sure you want to delete this enquiry?',
        type: 'red',
        buttons: {
            confirm: function () {
                document.getElementById('delete-form-' + id).submit();
            },
            cancel: function () {}
        }
    });
}
</script>
@endsection
