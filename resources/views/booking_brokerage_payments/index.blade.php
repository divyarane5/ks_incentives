@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

<h4 class="fw-bold py-3 mb-4">
Brokerage Invoice Management
</h4>

<div class="card">

<div class="card-body">

<table class="table table-bordered" id="invoiceTable">

<thead>
<tr>
<th>ID</th>
<th>Booking</th>
<th>Client</th>
<th>Project</th>
<th>Invoice %</th>
<th>Invoice Amount</th>
<th>Received</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

</table>

</div>
</div>
</div>

@endsection

@section('script')

<script>

$('#invoiceTable').DataTable({

processing:true,
serverSide:true,

ajax:"{{ route('brokerage-payments.datatable') }}",

columns:[

{data:'id'},
{data:'booking_id'},
{data:'client_name'},
{data:'project_name'},
{data:'invoice_percent'},
{data:'invoice_amount_format'},
{data:'received_amount_format'},
{data:'status_badge'},
{data:'action'}

]

});

</script>

@endsection