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
<input
type="number"
name="bank_received_amount"
class="form-control"
step="0.01">
</div>
<div class="col-md-6 mb-3">
    <label>TDS Amount</label>
    <input type="number"
           name="tds_amount"
           class="form-control"
           step="0.01">
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

{{-- ================= EDIT PAYMENT MODAL ================= --}}

<div class="modal fade" id="receivePaymentModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form method="POST"
      id="receiveForm"
      enctype="multipart/form-data">

@csrf
@method('PUT')

<div class="modal-header">
<h5 class="modal-title">Edit Invoice / Payment</h5>

<button type="button"
        class="btn-close"
        data-bs-dismiss="modal">
</button>
</div>

<div class="modal-body">

<div class="row">

<div class="col-md-6 mb-3">
<label>Invoice %</label>

<input type="number"
       step="0.01"
       name="invoice_percent"
       id="edit_invoice_percent"
       class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Invoice Amount</label>

<input type="number"
       step="0.01"
       name="invoice_amount"
       id="edit_invoice_amount"
       class="form-control"
       readonly>
</div>

<div class="col-md-6 mb-3">
<label>Invoice Date</label>

<input type="date"
       name="invoice_date"
       id="edit_invoice_date"
       class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Invoice File</label>

<input type="file"
       name="invoice_file"
       class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Bank Received Amount</label>

<input type="number"
       step="0.01"
       name="bank_received_amount"
       id="edit_received_amount"
       class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>TDS Amount</label>

<input type="number"
       step="0.01"
       name="tds_amount"
       id="edit_tds_amount"
       class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Bank Received Date</label>

<input type="date"
       name="bank_received_date"
       id="edit_received_date"
       class="form-control" step="0.01">
</div>


<div class="col-md-12 mb-3">
<label>Remarks</label>

<textarea name="remarks"
          id="edit_remarks"
          class="form-control"></textarea>
</div>

</div>

</div>

<div class="modal-footer">

<button type="submit"
        class="btn btn-success">
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
let totalInvoiceUsed = 0;
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
totalInvoiceUsed = 0;
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
let totalPercent = 0;
let totalInvoice = 0;
let totalReceived = 0;
let totalTds = 0;

data.forEach(function(p){
    totalPercent += parseFloat(p.invoice_percent ?? 0);
    totalInvoice += parseFloat(p.invoice_amount ?? 0);
    totalReceived += parseFloat(p.bank_received_amount ?? 0);
    totalTds += parseFloat(p.tds_amount ?? 0);
    totalInvoiceUsed += parseFloat(p.invoice_percent ?? 0);

    let badge = (p.status==='received')
    ? '<span class="badge bg-success">Received</span>'
    : '<span class="badge bg-warning">Invoice Raised</span>';

    let fileBtn = '';

    if(p.invoice_file){

        fileBtn = `
        <a href="{{ asset('storage') }}/${p.invoice_file}"
        target="_blank"
        class="btn btn-sm btn-info">
        View File
        </a>`;
    }

    let actionBtn = `
        <button type="button"
        class="btn btn-sm btn-primary update-payment"
        data-id="${p.id}"
        data-invoice_percent="${p.invoice_percent ?? ''}"
        data-invoice_amount="${p.invoice_amount ?? ''}"
        data-invoice_date="${p.invoice_date ?? ''}"
        data-bank_received_amount="${p.bank_received_amount ?? ''}"
        data-bank_received_date="${p.bank_received_date ?? ''}"
        data-tds_amount="${p.tds_amount ?? ''}"
        data-status="${p.status ?? ''}"
        data-remarks="${p.remarks ?? ''}">
        Edit
        </button>
        `;


    html += `
    <tr>

    <td>
        ${p.invoice_percent ?? 0}%
    </td>

    <td>
        ₹ ${Number(p.invoice_amount ?? 0).toLocaleString()}
        <br>
        <small class="text-muted">
            TDS: ₹ ${Number(p.tds_amount ?? 0).toLocaleString()}
        </small>
    </td>

    <td>
        ${p.invoice_date ?? '-'}
        <br>
        ${fileBtn}
    </td>

    <td>
        ₹ ${Number(p.bank_received_amount ?? 0).toLocaleString()}
    </td>

    <td>
        ${badge}
    </td>

    <td>
        ${actionBtn}
    </td>

    </tr>
    `;

});
html += `
<tr class="table-dark fw-bold">
<td>${totalPercent.toFixed(2)}%</td>
<td>₹ ${Number(totalInvoice).toLocaleString()}</td>
<td>Total</td>
<td>₹ ${Number(totalReceived).toLocaleString()}</td>
<td>TDS: ₹ ${Number(totalTds).toLocaleString()}</td>
<td>-</td>
</tr>
`;
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

let id = $(this).data('id');

$('#receiveForm').attr(
'action',
"{{ url('booking/payment-update') }}/"+id
);

$('#edit_invoice_percent').val($(this).data('invoice_percent'));

$('#edit_invoice_amount').val($(this).data('invoice_amount'));

$('#edit_invoice_date').val($(this).data('invoice_date'));

$('#edit_received_amount').val($(this).data('bank_received_amount'));

$('#edit_received_date').val($(this).data('bank_received_date'));

$('#edit_tds_amount').val($(this).data('tds_amount'));

$('#edit_status').val($(this).data('status'));

$('#edit_remarks').val($(this).data('remarks'));

$('#receivePaymentModal').modal('show');

});

/* ================= INVOICE AMOUNT CALCULATION ================= */

$('#invoice_percent').on('keyup change', function () {

    let percent = parseFloat($(this).val()) || 0;

    let agreement =
        parseFloat($('#agreement_value_raw').val()) || 0;

    let totalBrokeragePercent =
        parseFloat(
            $('#total_brokerage_percent')
            .val()
            .replace('%','')
        ) || 0;

    let allowedBalance =
        totalBrokeragePercent - totalInvoiceUsed;

    if(percent > allowedBalance){

        alert(
            'Invoice % exceeds remaining brokerage balance.\nRemaining Allowed: '
            + allowedBalance.toFixed(2) + '%'
        );

        $(this).val('');
        $('#invoice_amount').val('');

        return;
    }

    let amount = (agreement * percent) / 100;

    amount = amount.toFixed(2);

    $('#invoice_amount').val(amount);

    // AUTO 98% RECEIVED
    let received = (amount * 0.98).toFixed(2);

    // AUTO 2% TDS
    let tds = (amount * 0.02).toFixed(2);

    $('input[name="bank_received_amount"]').val(received);

    $('input[name="tds_amount"]').val(tds);

});

$('#edit_invoice_percent').on('keyup change', function () {

    let currentEditPercent =
        parseFloat($(this).val()) || 0;

    let originalPercent =
        parseFloat(
            $('.update-payment[data-id="' +
            $('#receiveForm').attr('action').split('/').pop()
            + '"]').data('invoice_percent')
        ) || 0;

    let totalBrokeragePercent =
        parseFloat(
            $('#total_brokerage_percent')
            .val()
            .replace('%','')
        ) || 0;

    let allowedBalance =
        totalBrokeragePercent
        - totalInvoiceUsed
        + originalPercent;

    if(currentEditPercent > allowedBalance){

        alert(
            'Invoice % exceeds allowed brokerage balance.\nAllowed: '
            + allowedBalance.toFixed(2) + '%'
        );

        $(this).val(originalPercent);

        return;
    }

    let agreement =
        parseFloat($('#agreement_value_raw').val()) || 0;

    let amount =
        (agreement * currentEditPercent) / 100;

    amount = amount.toFixed(2);

    $('#edit_invoice_amount').val(amount);

    // AUTO 98%
    let received = (amount * 0.98).toFixed(2);

    // AUTO 2%
    let tds = (amount * 0.02).toFixed(2);

    $('#edit_received_amount').val(received);

    $('#edit_tds_amount').val(tds);

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