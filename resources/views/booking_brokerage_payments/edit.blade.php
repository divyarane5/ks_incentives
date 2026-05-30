@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

<div class="card">

<div class="card-header d-flex justify-content-between">
    <h5>Edit Brokerage Invoice</h5>

    <a href="{{ route('brokerage-payments.index') }}"
       class="btn btn-secondary">
        Back
    </a>
</div>

<div class="card-body">

<form method="POST"
      action="{{ route('brokerage-payments.update',$payment->id) }}"
      enctype="multipart/form-data">

@csrf
@method('PUT')

<input type="hidden"
       id="agreement_value"
       value="{{ $payment->booking->agreement_value }}">

<input type="hidden"
       id="total_brokerage_percent"
       value="{{ $payment->booking->total_brokerage_percent }}">


<div class="row">

<div class="col-md-6 mb-3">
<label>Booking</label>

<input type="text"
       class="form-control"
       readonly
       value="#{{ $payment->booking->id }} -
              {{ $payment->booking->client_name }} -
              {{ optional($payment->booking->project)->name }}">
</div>

<div class="col-md-3 mb-3">
<label>Invoice %</label>

<input type="number"
       step="0.01"
       name="invoice_percent"
       id="invoice_percent"
       value="{{ $payment->invoice_percent }}"
       class="form-control">
</div>

<div class="col-md-3 mb-3">
<label>Invoice Amount</label>

<input type="number"
       step="0.01"
       name="invoice_amount"
       id="invoice_amount"
       value="{{ $payment->invoice_amount }}"
       class="form-control"
       readonly>
</div>

<div class="col-md-6 mb-3">
<label>Invoice Date</label>

<input type="date"
       name="invoice_date"
       value="{{ $payment->invoice_date }}"
       class="form-control">
</div>

<div class="col-md-6 mb-3">

<label>Invoice File</label>

@if($payment->invoice_file)

<a href="{{ asset('storage/'.$payment->invoice_file) }}"
   target="_blank"
   class="btn btn-info btn-sm d-block mb-2">

    View Current Invoice

</a>

@endif

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
       value="{{ $payment->bank_received_amount }}"
       class="form-control">
</div>

<div class="col-md-4 mb-3">
<label>TDS Amount</label>

<input type="number"
       step="0.01"
       name="tds_amount"
       id="tds_amount"
       value="{{ $payment->tds_amount }}"
       class="form-control">
</div>

<div class="col-md-4 mb-3">
<label>Received Date</label>

<input type="date"
       name="bank_received_date"
       value="{{ $payment->bank_received_date }}"
       class="form-control">
</div>

<div class="col-md-12 mb-3">

<label>Remarks</label>

<textarea name="remarks"
          class="form-control"
          rows="3">{{ $payment->remarks }}</textarea>

</div>

</div>

<button type="submit"
        class="btn btn-success">
    Update Invoice
</button>

</form>

</div>
</div>

</div>

@endsection

@section('script')

<script>

function calculateInvoice() {

    let agreement =
        parseFloat($('#agreement_value').val()) || 0;

    let percent =
        parseFloat($('#invoice_percent').val()) || 0;

    let maxPercent =
        parseFloat($('#total_brokerage_percent').val()) || 0;

    if(percent <= 0){

        $('#invoice_amount').val('');
        return;
    }

    if(percent > maxPercent){

        alert(
            'Invoice % cannot exceed Total Brokerage % (' +
            maxPercent + '%)'
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

$('#invoice_percent').on('keyup change', function(){

    calculateInvoice();

});

</script>

@endsection