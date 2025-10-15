@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-4 col-md-6"><span class="text-muted fw-light">Invoice /</span> List</h4>
        <div class="col-md-6">
            @can('invoice-create')
            <a href="{{ route('invoice.create') }}" type="button" class="btn btn-primary pull-right my-3 mb-4 ">Add Invoice</a>
            @endcan
        </div>
    </div>

    <!-- Striped Rows -->
    <div class="card">
        <h5 class="card-header">Invoices</h5>
        <div class="table-responsive text-nowrap">
            <table id="invoice-datatable" class="table table-striped" width="100%">
            <thead>
                <tr>
                    <th>Booking</th>
                    <th>Total <br>Brokerage</th>
                    <th>Base <br>Revenue</th>
                    <th>Invoice Date</th>
                    <th>Invoice Percent</th>
                    <th>Invoice Amount</th>
                    
                    
                    <th>TDS</th>
                    <th>Payment Received</th>
                    <th>Passback Amount</th>
                    <th>Actual Revenue</th>
                    <th>Salary</th>
                    <th>Pending<br> Invoice Percent</th>
                    <th>Pending <br>Invoice Amount</th>
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
      var table = $('#invoice-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('invoice.index') }}",
            columns: [
                {data: 'booking_id', name: 'invoices.booking_id'},
                {data: 'total_brokerage'},
                {data: 'base_revenue'},
                {data: 'invoice_date', name: 'invoices.invoice_date'},
                {data: 'invoice_percent', name: 'invoices.invoice_percent'},
                {data: 'invoice_amount', name: 'invoices.invoice_amount'},
                {data: 'tds'},
                {data: 'payment_received', name: 'invoices.payment_received'},
                {data: 'passback_amount'},
                {data: 'actual_revenue'},
                {data: 'salary'},
                {data: 'p_invoice_percent'},
                {data: 'p_invoice_amount'},
                {data: 'created_at', name: 'invoices.created_at'},
                {data: 'updated_at', name: 'invoices.updated_at'},
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
