@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Booking /</span> List</h4>
        <div class="col-md-6">
            @can('booking-create')
            <a href="{{ route('booking.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4">Add Booking</a>
            @endcan
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Bookings</h5>
        <div class="table-responsive text-nowrap">
            <table id="booking-datatable" class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Booking Date</th>
                        <th>Client Name</th>
                        <th>Client Contact</th>
                        <th>Lead Source</th>
                        <th>Project Name</th>
                        <th>Developer Name</th>
                        <!-- <th>Tower</th>
                        <th>Wing</th>
                        <th>Flat No</th>
                        <th>Configuration</th> -->
                        <th>Booking Amount</th>
                        <th>Agreement Value</th>
                        <th>Base Brokerage %</th>
                        <th>Site Ladder Increment %</th>
                        <th>AOP Brokerage %</th>
                        <th>Total Brokerage %</th>
                        <th>Revenue</th>
                        <!-- <th>Total Paid Amount</th>
                        <th>Pending Amount</th> -->
                        <th>Additional Kicker</th>
                        <th>Passback</th>
                        <th>Final Revenue</th>
                        <th>Sales Manager</th>
                        <th>TL</th>
                        <th>Sr TL</th>
                        <th>Cluster Head</th>
                        <!-- <th>Booking Confirm</th>
                        <th>Registration Confirm</th>
                        <th>Registration Date</th>
                        <th>Invoice Raised</th>
                        <th>Remark</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Deleted At</th> -->
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
        var table = $('#booking-datatable').DataTable({
            processing: true,
            serverSide: true,
            // ajax: {
            //     url: "{{ route('booking.index') }}",
            //     type: "POST",
            //     data: {
            //         _token: "{{ csrf_token() }}"
            //     }
            // },
             ajax: "{{ route('booking.index') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'booking_date', name: 'booking_date'},
                {data: 'client_name', name: 'client_name'},
                {data: 'client_contact', name: 'client_contact'},
                {data: 'lead_source', name: 'lead_source'},
                {data: 'project_name', name: 'project.name', defaultContent: '-'},
                {data: 'developer_name', name: 'developer.name', defaultContent: '-'},
                // {data: 'tower', name: 'tower'},
                // {data: 'wing', name: 'wing'},
                // {data: 'flat_no', name: 'flat_no'},
                // {data: 'configuration', name: 'configuration'},
                {data: 'booking_amount', name: 'booking_amount'},
                {data: 'agreement_value', name: 'agreement_value'},
                {data: 'base_brokerage_percent', name: 'base_brokerage_percent'},
                {data: 'site_increment_percent', name: 'site_increment_percent'},
                {data: 'aop_ladder_percent', name: 'aop_ladder_percent'},
                {data: 'total_brokerage_percent', name: 'total_brokerage_percent'},
                {data: 'current_effective_amount', name: 'current_effective_amount'}, // Label now "Revenue"
                // {data: 'total_paid_amount', name: 'total_paid_amount'},
                // {data: 'pending_amount', name: 'pending_amount'},
                {data: 'additional_kicker', name: 'additional_kicker'},
                {data: 'passback', name: 'passback'},
                {data: 'final_revenue', name: 'final_revenue'},
                {data: 'sales_manager', name: 'sales_manager', defaultContent: '-'},
                {data: 'tl', name: 'tl', defaultContent: '-'},
                {data: 'sr_tl', name: 'sr_tl', defaultContent: '-'},
                {data: 'cluster_head', name: 'cluster_head', defaultContent: '-'},
                // {data: 'booking_confirm', orderable:false, searchable:false},
                // {data: 'registration_confirm', orderable:false, searchable:false},
                // {data: 'registration_date', name: 'registration_date'},
                // {data: 'invoice_raised', orderable:false, searchable:false},
                // {data: 'remark', name: 'remark'},
                // {data: 'created_by', name: 'created_by'},
                // {data: 'created_at', name: 'created_at'},
                // {data: 'updated_at', name: 'updated_at'},
                // {data: 'deleted_at', name: 'deleted_at'},
                {data: 'action', orderable:false, searchable:false},
            ]
        });
    });
</script>
@endsection