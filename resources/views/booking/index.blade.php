@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Booking /</span> List</h4>
        <div class="col-md-6">
            @can('booking-create')
            <a href="{{ route('booking.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Booking</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Bookings</h5>
        <div class="table-responsive text-nowrap">
            <table id="booking-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Developer Name</th>
                    <th>Client Name</th>
                    <th>Client Contact</th>
                    <th>Booking Date</th>
                    <th>Configuration</th>
                    <th>Tower</th>
                    <th>Wing</th>
                    <th>Flat No.</th>
                    <th>Sourcing Manager</th>
                    <th>Sourcing Contact</th>
                    <th>Sales Person</th>
                    <th>Team Manager</th>
                    <th>Agreement Value</th>
                    <th>Booking Amount</th>
                    <th>Base Brokerage</th>
                    <th>Revenue</th>
                    <th>Passback Given</th>
                    <th>Company Revenue</th>
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
      var table = $('#booking-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('booking.index') }}",
            columns: [
                {data: 'project_name', name: 'project_name'},
                {data: 'developer_name', name: 'developer_name'},
                {data: 'client_name', name: 'client_name'},
                {data: 'client_contact', name: 'client_contact'},
                {data: 'booking_date', name: 'booking_date'},
                {data: 'configuration', name: 'configuration'},
                {data: 'tower', name: 'tower'},
                {data: 'wing', name: 'wing'},
                {data: 'flat_no', name: 'flat_no'},
                {data: 'sourcing_manager', name: 'sourcing_manager'}, 
                {data: 'sourcing_contact', name: 'sourcing_contact'}, 
                {data: 'sales_person', name: 'sales_person'},
                {data: 'reporting_person'},
                {data: 'agreement_value', name: 'agreement_value'},
                {data: 'booking_amount', name: 'booking_amount'},
                {data: 'brokerage'},
                {data: 'revenue'},
                {data: 'passback', name: 'passback'},
                {data: 'company_revenue'},
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'action', 'sortable': false},
            ]
      });

    });

    function deleteBooking(id)
    {
        $.confirm({
            title: 'Delete Booking',
            content: 'Are you sure you want to delete Booking?',
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
