@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6">
            <span class="text-muted fw-light">Channel Partners /</span> List
        </h4>
        <div class="col-md-6">
            @can('channel-partner-create')
            <a href="{{ route('channel_partners.create') }}" class="btn btn-primary pull-right my-3 mb-4">
                + Add Channel Partner
            </a>
            @endcan
        </div>
    </div>
    <div class="card my-4" id="ChannelPartnerFilter">
        <div class="card-body row">

            <div class="mb-3 col-md-4">
                <label class="form-label">Sourcing Manager</label>
                <select id="sourcing_manager_id" class="form-select">
                    <option value="">All</option>
                    @foreach($sourcingManagers as $manager)
                        <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">&nbsp;</label><br>
                <button id="filter" class="btn btn-primary">Filter</button>
                <button id="clear" class="btn btn-secondary">Clear</button>
            </div>

        </div>
    </div>


    <div class="card">
        <h5 class="card-header">Channel Partners</h5>
        <div class="table-responsive text-nowrap">
            <table id="channel-partners-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>Firm Name</th>
                        <th>Owner Name</th>
                        <th>Contact</th>
                        <th>RERA</th>
                        <th>Operational Locations</th>
                        <th>Office Locations</th>
                        <th>Sourcing Manager</th>
                        <th>Acquisition Channel</th>
                        <th>Property Type</th>
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
<script>
$(document).ready(function () {

    var table = $('#channel-partners-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('channel_partners.index') }}",
            data: function (d) {
                d.sourcing_manager_id = $('#sourcing_manager_id').val();
            }
        },
        columns: [
            { data: 'firm_name' },
            { data: 'owner_name' },
            { data: 'contact' },
            { data: 'rera_number' },
            { data: 'operational_locations', orderable: false, searchable: false },
            { data: 'office_locations', orderable: false, searchable: false },
            { data: 'sourcing_manager' },
            { data: 'acquisition_channel' },
            { data: 'property_type' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    $('#filter').click(function () {
        table.ajax.reload();
    });

    $('#clear').click(function () {
        $('#sourcing_manager_id').val('');
        table.ajax.reload();
    });

});

function deleteChannelPartner(id) {
    $.confirm({
        title: 'Delete Channel Partner',
        content: 'Are you sure you want to delete this partner?',
        type: 'red',
        typeAnimated: true,
        buttons: {
            confirm: {
                text: 'Yes',
                btnClass: 'btn-red',
                action: function() {
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
