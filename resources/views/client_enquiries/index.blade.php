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

    <div class="card">
        <h5 class="card-header">Client Enquiries</h5>
        <div class="table-responsive text-nowrap">
            <table id="client-enquiry-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact No</th>
                        <th>Property Type</th>
                        <th>Purchase Purpose</th>
                        <th>Funding Source</th>
                        <th>Source of Visit</th>
                        <th>Channel Partner</th>
                        <th>Closing Manager</th>
                        <th>Created At</th>
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
            ajax: "{{ route('client-enquiries.index') }}",
            columns: [
                {data: 'id', name: 'client_enquiries.id'},
                {data: 'customer_name', name: 'client_enquiries.customer_name'},
                {data: 'contact_no', name: 'client_enquiries.contact_no'},
                {data: 'property_type', name: 'client_enquiries.property_type'},
                {data: 'purchase_purpose', name: 'client_enquiries.purchase_purpose'},
                {data: 'funding_source', name: 'client_enquiries.funding_source'},
                {data: 'source_of_visit', name: 'client_enquiries.source_of_visit'},
                {data: 'channel_partner', name: 'channel_partner.firm_name'},
                {data: 'closing_manager', name: 'closing_manager.name'},
                {data: 'created_at', name: 'client_enquiries.created_at'},
                {data: 'action', orderable: false, searchable: false},
            ]
        });
    });

    function deleteEnquiry(id) {
        $.confirm({
            title: 'Delete Enquiry',
            content: 'Are you sure you want to delete this enquiry?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: 'Yes',
                    btnClass: 'btn-red',
                    action: function(){
                        event.preventDefault();
                        document.getElementById('delete-form-' + id).submit();
                    }
                },
                cancel: function () {}
            }
        });
    }
</script>
@endsection
