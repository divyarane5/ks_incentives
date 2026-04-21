@extends('layouts.app')

@section('content')

<style>
.team-hover{
    cursor:pointer;
    text-decoration: underline dotted;
}
#booking-datatable select {
    width: 115px;
}
</style>
<div id="statusMsg" style="position:fixed; top:20px; right:20px; z-index:9999;"></div>
<div class="container-xxl flex-grow-1 container-p-y">

<div class="row">
    <h4 class="fw-bold py-3 mb-4 col-md-6">
        <span class="text-muted fw-light">Booking /</span> List
    </h4>

    <div class="col-md-6 text-end">
        @can('booking-create')
        <a href="{{ route('booking.create') }}" class="btn btn-primary">
            Add Booking
        </a>
        @endcan
    </div>
</div>

<div class="card">
<h5 class="card-header">Bookings</h5>

<div class="table-responsive">
<table id="booking-datatable" class="table table-striped">

<thead>
<tr>
<th>ID</th>
<th>Booking Date</th>
<th>Client Name</th>
<th>Client Contact</th>
<th>Lead Source</th>
<th>Project</th>
<th>Developer</th>
<th>Booking Amount</th>
<th>Agreement Value</th>
<th>Total Brokerage %</th>
<th>Revenue</th>
<th>Final Revenue</th>
<th>Team Hierarchy</th>
<th>Booking Status</th>
<th>Total Invoice %</th>
<th>Total Invoice Amount</th>
<th>Total Received</th>
<th>Pending %</th>
<th>Pending Amount</th>
<th>Payment Status</th>
<th>Actions</th>
</tr>
</thead>

<tbody></tbody>

</table>
</div>
</div>
</div>



{{-- ================= PAYMENT MODAL ================= --}}

<div class="modal fade" id="addPaymentModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="POST"
      action="{{ route('booking.brokerage.payment.store') }}"
      enctype="multipart/form-data">

@csrf

<input type="hidden" name="booking_id" id="booking_id">
<input type="hidden" id="agreement_value_raw">
<input type="hidden" id="brokerage_amount_raw">

<div class="modal-header">
<h5 class="modal-title">Brokerage Payments</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

{{-- SUMMARY --}}
<div class="row mb-3">

<div class="col-md-4">
<label>Agreement Value</label>
<input type="text" id="agreement_value" class="form-control" readonly>
</div>

<div class="col-md-4">
<label>Total Brokerage %</label>
<input type="text" id="total_brokerage_percent" class="form-control" readonly>
</div>

<div class="col-md-4">
<label>Total Brokerage Amount</label>
<input type="text" id="total_brokerage_amount" class="form-control" readonly>
</div>

</div>

<hr>

{{-- HISTORY --}}
<h6>Invoice History</h6>

<table class="table table-bordered table-sm">

<thead>
<tr>
<th>%</th>
<th>Invoice Amount</th>
<th>Invoice Date</th>
<th>Received</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody id="paymentHistory">
<tr>
<td colspan="6" class="text-center">Loading...</td>
</tr>
</tbody>

</table>

<hr>

{{-- ADD INVOICE --}}
<h6>Add New Invoice</h6>

<div class="row">

<div class="col-md-6 mb-3">
<label>Invoice %</label>
<input
type="number"
name="invoice_percent"
id="invoice_percent"
class="form-control"
step="0.01"
min="0">
</div>

<div class="col-md-6 mb-3">
<label>Invoice Amount</label>
<input
type="number"
name="invoice_amount"
id="invoice_amount"
class="form-control"
readonly>
</div>

<div class="col-md-6 mb-3">
<label>Invoice Date</label>
<input type="date" name="invoice_date" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Invoice File</label>
<input type="file" name="invoice_file" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Bank Received Amount</label>
<input type="number" name="bank_received_amount" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Bank Received Date</label>
<input type="date" name="bank_received_date" class="form-control">
</div>

<div class="col-md-12 mb-3">
<label>Remarks</label>
<textarea name="remarks" class="form-control"></textarea>
</div>

</div>

</div>

<div class="modal-footer">
<button type="submit" class="btn btn-success" id="savePaymentBtn">
Save Payment
</button>
</div>

</form>

</div>
</div>
</div>



{{-- ================= UPDATE PAYMENT MODAL ================= --}}

<div class="modal fade" id="receivePaymentModal">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST" id="receiveForm">
@csrf

<div class="modal-header">
<h5 class="modal-title">Update Payment</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="mb-3">
<label>Received Amount</label>
<input type="number"
       name="bank_received_amount"
       id="update_received_amount"
       class="form-control">
</div>

<div class="mb-3">
<label>Received Date</label>
<input type="date"
       name="bank_received_date"
       id="update_received_date"
       class="form-control">
</div>

</div>

<div class="modal-footer">
<button type="submit" class="btn btn-success">
Update Payment
</button>
</div>

</form>

</div>
</div>
</div>

@endsection



@section('script')

<script>

$(document).ready(function(){

/* ================= DATATABLE ================= */

$('#booking-datatable').DataTable({

processing:true,
serverSide:true,
ajax:"{{ route('booking.index') }}",

columns:[
{data:'id'},
{data:'booking_date'},
{data:'client_name'},
{data:'client_contact'},
{data:'lead_source'},
{data:'project_name'},
{data:'developer_name'},
{data:'booking_amount'},
{data:'agreement_value'},
{data:'total_brokerage_percent'},
{data:'current_effective_amount'},
{data:'final_revenue'},
{data:'team_hierarchy',orderable:false,searchable:false},
{data:'booking_confirm'},
{data:'total_invoice_percent'},
{data:'total_invoice_amount'},
{data:'total_received_amount'},
{data:'pending_brokerage_percent'},
{data:'pending_brokerage_amount'},
{data:'payment_status'},
{data:'action',orderable:false,searchable:false},
]

});

});


/* ================= OPEN PAYMENT MODAL ================= */

$(document).on('click','.add-payment',function(){

// reset form
$('#invoice_percent').val('');
$('#invoice_amount').val('');
$('input[name="invoice_date"]').val('');
$('input[name="bank_received_amount"]').val('');
$('input[name="bank_received_date"]').val('');
$('textarea[name="remarks"]').val('');

let booking_id = $(this).data('id');
let agreement  = $(this).data('agreement');
let percent    = $(this).data('percent');
let brokerage  = $(this).data('brokerage');
let status     = $(this).data('status');

$('#booking_id').val(booking_id);

$('#agreement_value').val('₹ '+Number(agreement).toLocaleString());
$('#total_brokerage_percent').val(percent+'%');
$('#total_brokerage_amount').val('₹ '+Number(brokerage).toLocaleString());

$('#agreement_value_raw').val(agreement);
$('#brokerage_amount_raw').val(brokerage);


/* ================= HISTORY AJAX ================= */

$('#paymentHistory').html(
'<tr><td colspan="6" class="text-center">Loading...</td></tr>'
);

$.get("{{ url('booking/payment-history') }}/"+booking_id,function(data){

let html='';

if(data.length>0){

data.forEach(function(p){

let badge = (p.status==='received')
? '<span class="badge bg-success">Received</span>'
: '<span class="badge bg-warning">Invoice Raised</span>';

let actionBtn = '';

if(p.status!=='received'){

actionBtn = `
<button type="button"
class="btn btn-sm btn-primary update-payment"
data-id="${p.id}"
data-amount="${p.bank_received_amount ?? ''}"
data-date="${p.bank_received_date ?? ''}">
Update
</button>`;

}

html += `

<tr>
<td>${p.invoice_percent ?? 0}%</td>
<td>₹ ${Number(p.invoice_amount ?? 0).toLocaleString()}</td>
<td>${p.invoice_date ?? '-'}</td>
<td>₹ ${Number(p.bank_received_amount ?? 0).toLocaleString()}</td>
<td>${badge}</td>
<td>${actionBtn}</td>
</tr>

`;

});

}else{

html = '<tr><td colspan="6" class="text-center">No history found</td></tr>';

}

$('#paymentHistory').html(html);

});


/* ================= DISABLE IF COMPLETED ================= */

if(status === 'completed'){

$('#addPaymentModal input, #addPaymentModal textarea')
.prop('disabled',true);

$('#savePaymentBtn').hide();

}else{

$('#addPaymentModal input, #addPaymentModal textarea')
.prop('disabled',false);

$('#savePaymentBtn').show();

}

$('#addPaymentModal').modal('show');

});


/* ================= UPDATE PAYMENT ================= */

$(document).on('click','.update-payment',function(){

let id     = $(this).data('id');
let amount = $(this).data('amount');
let date   = $(this).data('date');

$('#receiveForm').attr(
'action',
"{{ url('booking/payment-update') }}/"+id
);

$('#update_received_amount').val(amount);
$('#update_received_date').val(date);

$('#receivePaymentModal').modal('show');

});


/* ================= INVOICE AMOUNT CALCULATION ================= */

$('#invoice_percent').on('keyup change',function(){

let percent   = $(this).val();
let agreement = $('#agreement_value_raw').val();

if(percent && agreement){

let amount = (agreement * percent) / 100;

$('#invoice_amount').val(Math.round(amount));

}

});


/* ================= TOOLTIP FIX ================= */

$(document).on('draw.dt',function(){
$('[data-bs-toggle="tooltip"]').tooltip({html:true});
});

</script>
<script>
function updateBStatus(el, bookingId) {

    let status = el.value;

    fetch("{{ route('booking.update_bstatus') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            id: bookingId,
            booking_confirm: status
        })
    })
    .then(res => res.json())
        .then(data => {

        $('#statusMsg').html(`
            <div class="alert alert-success alert-dismissible fade show">
                ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

        setTimeout(() => {
            $('#statusMsg').html('');
        }, 3000);

    })
    .catch(err => console.error(err));
}

</script>
@endsection