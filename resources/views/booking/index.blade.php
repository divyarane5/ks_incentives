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
                    <th>Booking <br>Date</th>
                    <th>Client<br>Name </th>
                    <th>Project <br>Name</th>
                    <th>Developer <br>Name</th>
                    <th>Agreement<br>Value</th>
                    <th>Base<br> Brokerage</th>
                    <th>Site<br> Brokerage</th>
                    <th>AOP <br>Brokerage</th>
                    <th>Total <br>Brokerage</th>
                    <th>Base <br>Revenue</th>
                    <th>TDS</th>
                    <th>Net Base <br>Revenue</th>
                    <th>Passback <br>Given</th>
                    <th>Actual <br>Revenue</th>
                    <th>Additional <br>Kicker</th>
                    <th>Total <br>Revenue</th>
                    <th>SM Name</th>
                    <th>TL Name</th>
                    <th>Sr. TL Name</th>
                    <th>CH</th>
                    <th>Booking <br>Confirm</th>
                    <th>Registration <br>Confirm</th>
                    <th>Invoice  <br>Raised</th>
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
                
                {data: 'booking_date', name: 'booking_date'},
                {data: 'client_name', name: 'client_name'},
                {data: 'project_name', name: 'project_name'},
                {data: 'developer_name', name: 'developer_name'},
                {data: 'agreement_value', name: 'agreement_value'},
                {data: 'brokerage'},
                {data: 'project_brokerage'},
                {data: 'aop_brokerage'},
                {data: 'total_brokerage'},
                {data: 'base_revenue'},
                {data: 'tds'},
                {data: 'net_base_revenue'},
                {data: 'passback', name: 'passback'},
                {data: 'actual_revenue'},
                {data: 'additional_kicker', name: 'additional_kicker'},
                {data: 'total_revenue'},
                {data: 'sales_person', name: 'sales_person'},
                {data: 'team_leader'},
                {data: 'sr_team_leader'},
                {data: 'cluster_head'},
                {data: 'booking_confirm', name: 'booking_confirm'},
                {data: 'registration_confirm', name: 'registration_confirm'},
                {data: 'invoice_raised', name: 'invoice_raised'},
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

    function updateStatus(element, bookingId)
    {
        var registration_confirm = $(element).is(':checked') ? 1 : 0;
        $.ajax({
            type: 'POST',
            url: "{{ route('booking.update_status') }}",
            headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
            },
            data: {registration_confirm: registration_confirm, id: bookingId},
            success: function (res) {
                $.alert({
                    title: 'Success!',
                    content: 'Registration status updated successfully',
                    type: 'green',
                    typeAnimated: true,
                });
            }
        });
    }
    function updateIStatus(element, bookingId)
    {
        var invoice_raised = $(element).is(':checked') ? 1 : 0;
        $.ajax({
            type: 'POST',
            url: "{{ route('booking.update_istatus') }}",
            headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
            },
            data: {invoice_raised: invoice_raised, id: bookingId},
            success: function (res) {
                $.alert({
                    title: 'Success!',
                    content: 'Invoice status updated successfully',
                    type: 'green',
                    typeAnimated: true,
                });
            }
        });
    }
    function updateBStatus(element, bookingId)
    {
        var booking_confirm = $(element).is(':checked') ? 1 : 0;
        $.ajax({
            type: 'POST',
            url: "{{ route('booking.update_bstatus') }}",
            headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
            },
            data: {booking_confirm: booking_confirm, id: bookingId},
            success: function (res) {
                $.alert({
                    title: 'Success!',
                    content: 'Booking status updated successfully',
                    type: 'green',
                    typeAnimated: true,
                });
            }
        });
    }
    
</script>
@endsection
