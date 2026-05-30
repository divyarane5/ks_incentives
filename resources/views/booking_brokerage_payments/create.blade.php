@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

<div class="card">

<div class="card-header">
    <h5>Add Brokerage Invoice</h5>
</div>

<div class="card-body">

<form method="POST"
      action="{{ route('brokerage-payments.store') }}"
      enctype="multipart/form-data">

@csrf

<div class="row">

<div class="col-md-6 mb-3">
<label>Booking</label>

<select name="booking_id"
        class="form-control"
        required>

<option value="">Select Booking</option>

@foreach($bookings as $booking)

<option value="{{ $booking->id }}"
        data-agreement="{{ $booking->agreement_value }}"
        data-brokerage="{{ $booking->total_brokerage_percent }}"
        data-used-percent="{{ $booking->total_invoice_percent }}">

    #{{ $booking->id }}
    -
    {{ $booking->client_name }}
    -
    {{ optional($booking->project)->name }}

</option>

@endforeach

</select>

</div>

<div class="col-md-3 mb-3">
<label>Invoice %</label>
<input type="number"
       step="0.01"
       name="invoice_percent"
       id="invoice_percent"
       class="form-control">
</div>

<div class="col-md-3 mb-3">
<label>Invoice Amount</label>
<input type="number"
       step="0.01"
       name="invoice_amount"
       id="invoice_amount"
       class="form-control"
       readonly>
</div>

<div class="col-md-6 mb-3">
<label>Invoice Date</label>
<input type="date"
       name="invoice_date"
       class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Invoice File</label>
<input type="file"
       name="invoice_file"
       class="form-control">
</div>

<div class="col-md-4 mb-3">
<label>Received Amount</label>
<input type="number"
       step="0.01"
       name="bank_received_amount"
       id="bank_received_amount"
       class="form-control">
</div>

<div class="col-md-4 mb-3">
<label>TDS Amount</label>
<input type="number"
       step="0.01"
       name="tds_amount"
       id="tds_amount"
       class="form-control">
</div>

<div class="col-md-4 mb-3">
<label>Received Date</label>
<input type="date"
       name="bank_received_date"
       class="form-control">
</div>

<div class="col-md-12 mb-3">
<label>Remarks</label>
<textarea name="remarks"
          class="form-control"></textarea>
</div>

</div>

<button type="submit"
        class="btn btn-success">
    Save Invoice
</button>

</form>

</div>
</div>

</div>

@endsection

@section('script')

<script>

function calculateInvoice() {

    let selectedBooking = $('select[name="booking_id"] option:selected');

    let agreement =
        parseFloat(selectedBooking.data('agreement')) || 0;

    let totalBrokeragePercent =
        parseFloat(selectedBooking.data('brokerage')) || 0;

    let alreadyUsed =
        parseFloat(selectedBooking.data('used-percent')) || 0;

    let percent =
        parseFloat($('#invoice_percent').val()) || 0;

    let balancePercent =
        totalBrokeragePercent - alreadyUsed;

    if(percent > balancePercent){

        alert(
            'Invoice % exceeds remaining brokerage balance.\nRemaining Allowed: '
            + balancePercent.toFixed(2) + '%'
        );

        $('#invoice_percent').val('');
        $('#invoice_amount').val('');

        return;
    }

    let amount = (agreement * percent) / 100;

    $('#invoice_amount').val(amount.toFixed(2));

    $('#bank_received_amount').val(
        (amount * 0.98).toFixed(2)
    );

    $('#tds_amount').val(
        (amount * 0.02).toFixed(2)
    );
}

$('select[name="booking_id"]').on('change', function () {
    calculateInvoice();
});

$('#invoice_percent').on('keyup change', function () {
    calculateInvoice();
});

</script>

@endsection