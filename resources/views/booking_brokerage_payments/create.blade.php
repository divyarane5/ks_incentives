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

    <!-- LEFT SIDE -->
    <div class="col-lg-8">

        <!-- ================= Booking Information ================= -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">
                <strong>Booking Information</strong>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Booking
                        </label>

                        <select name="booking_id"
                                id="booking_id"
                                class="form-select"
                                required>

                            <option value="">
                                Select Booking
                            </option>

                            @foreach($bookings as $booking)

                            <option
                                value="{{ $booking->id }}"
                                data-agreement="{{ $booking->agreement_value }}"
                                data-brokerage="{{ $booking->total_brokerage_percent }}"
                                data-used-percent="{{ $booking->total_invoice_percent }}"
                                data-final-revenue="{{ $booking->final_revenue }}"
                                data-client="{{ $booking->client_name }}"
                                data-developer="{{ $booking->developer_id }}"
                                data-project="{{ optional($booking->project)->name }}"
                            >

                                #{{ $booking->id }}
                                -
                                {{ $booking->client_name }}
                                -
                                {{ optional($booking->project)->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Client</label>

                        <input
                            type="text"
                            id="client_name"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Project</label>

                        <input
                            type="text"
                            id="project_name"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label>Agreement Value</label>

                        <input
                            type="text"
                            id="agreement_value"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label>Total Brokerage %</label>

                        <input
                            type="text"
                            id="brokerage_percent"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label>Remaining %</label>

                        <input
                            type="text"
                            id="remaining_percent"
                            class="form-control"
                            readonly>

                    </div>

                </div>

            </div>

        </div>

        <!-- ================= Invoice Details ================= -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">

                <strong>Invoice Details</strong>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label>Invoice Number</label>

                        <input
                            type="text"
                            name="invoice_number"
                            class="form-control">

                    </div>

                    <div class="col-md-4 mb-3">

                        <label>Invoice Type</label>

                        <select
                            name="invoice_type"
                            id="invoice_type"
                            class="form-select">

                            <option value="tax_invoice">
                                Tax Invoice
                            </option>

                            <option value="proforma">
                                Proforma Invoice
                            </option>

                            <option value="credit_note">
                                Credit Note
                            </option>

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label>Invoice Date</label>

                        <input
                            type="date"
                            name="invoice_date"
                            class="form-control">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Invoice %</label>

                        <input
                            type="number"
                            step="0.01"
                            name="invoice_percent"
                            id="invoice_percent"
                            class="form-control">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label>Invoice Amount</label>

                        <input
                            type="number"
                            step="0.01"
                            name="invoice_amount"
                            id="invoice_amount"
                            class="form-control"
                            readonly>

                    </div>

                </div>

            </div>

        </div>

        <!-- ================= GST ================= -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">

                <strong>GST Details</strong>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <label>CGST %</label>

                        <input
                            type="number"
                            name="cgst_percent"
                            id="cgst_percent"
                            value="9"
                            class="form-control">

                    </div>

                    <div class="col-md-3">

                        <label>CGST Amount</label>

                        <input
                            type="number"
                            name="cgst_amount"
                            id="cgst_amount"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="col-md-3">

                        <label>SGST %</label>

                        <input
                            type="number"
                            name="sgst_percent"
                            id="sgst_percent"
                            value="9"
                            class="form-control">

                    </div>

                    <div class="col-md-3">

                        <label>SGST Amount</label>

                        <input
                            type="number"
                            name="sgst_amount"
                            id="sgst_amount"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="col-md-6 mt-3">

                        <label>Total GST</label>

                        <input
                            type="number"
                            name="total_gst_amount"
                            id="total_gst_amount"
                            class="form-control"
                            readonly>

                    </div>

                    <div class="col-md-6 mt-3">

                        <label>Grand Invoice</label>

                        <input
                            type="number"
                            id="grand_invoice"
                            class="form-control"
                            readonly>

                    </div>

                </div>

            </div>

        </div>

        <!-- ================= Collection ================= -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">

                <strong>Collection Details</strong>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">

                        <label>Amount Received from Developer (Incl GST)</label>

                        <input
                            type="number"
                            name="actual_receipt_amount"
                            id="actual_receipt_amount"
                            class="form-control">

                    </div>

                    <div class="col-md-4">

                        <label>Brokerage Received</label>

                        <input
                            type="number"
                            name="bank_received_amount"
                            id="bank_received_amount"
                            class="form-control">

                    </div>

                    <div class="col-md-4">

                        <label>TDS</label>

                        <input
                            type="number"
                            name="tds_amount"
                            id="tds_amount"
                            class="form-control">

                    </div>

                    <div class="col-md-6 mt-3">

                        <label>Received Date</label>

                        <input
                            type="date"
                            name="bank_received_date"
                            class="form-control">

                    </div>

                </div>

            </div>

        </div>

        <!-- ================= Billing ================= -->

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">

                <strong>Billing Details</strong>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">

                        <label>Developer Billing Entity</label>

                        <select
                            name="developer_billing_entity_id"
                             id="developer_billing_entity_id"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                        </select>

                    </div>

                    <div class="col-md-6">

                        <label>Company Bank Account</label>

                        <select
                            name="company_bank_account_id"
                             id="company_bank_account_id"
                            class="form-select">

                            <option value="">
                                Select
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </div>

        <!-- ================= Credit Note ================= -->

        <div
            class="card shadow-sm mb-4"
            id="creditNoteCard"
            style="display:none;">

            <div class="card-header bg-danger text-white">

                <strong>Credit Note</strong>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">

                        <label>Credit Note No.</label>

                        <input
                            type="text"
                            name="credit_note_number"
                            class="form-control">

                    </div>

                    <div class="col-md-4">

                        <label>Date</label>

                        <input
                            type="date"
                            name="credit_note_date"
                            class="form-control">

                    </div>

                    <div class="col-md-4">

                        <label>Reason</label>

                        <input
                            type="text"
                            name="credit_note_reason"
                            class="form-control">

                    </div>

                </div>

            </div>

        </div>

        <!-- ================= Attachment ================= -->

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white">

                <strong>Attachment</strong>

            </div>

            <div class="card-body">

                <div class="mb-3">

                    <label>Invoice File</label>

                    <input
                        type="file"
                        name="invoice_file"
                        class="form-control">

                </div>

                <div class="mb-3">

                    <label>Remarks</label>

                    <textarea
                        name="remarks"
                        rows="4"
                        class="form-control"></textarea>

                </div>

            </div>

        </div>

    </div>

    <!-- RIGHT SIDE -->

    <div class="col-lg-4">

        <div class="card sticky-top">

            <div class="card-header bg-success text-white">

                <strong>Invoice Summary</strong>

            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <tr>
                        <th>Agreement</th>
                        <td id="sumAgreement">0</td>
                    </tr>

                    <tr>
                        <th>Brokerage %</th>
                        <td id="sumBrokerage">0%</td>
                    </tr>

                    <tr>
                        <th>Remaining %</th>
                        <td id="sumRemaining">0%</td>
                    </tr>

                    <tr>
                        <th>Invoice Amount</th>
                        <td id="sumInvoice">0</td>
                    </tr>

                    <tr>
                        <th>GST</th>
                        <td id="sumGST">0</td>
                    </tr>

                    <tr>
                        <th>Grand Total</th>
                        <td id="sumGrand">0</td>
                    </tr>

                    <tr>
                        <th>Developer Receipt</th>
                        <td id="sumReceipt">0</td>
                    </tr>

                    <tr>
                        <th>TDS</th>
                        <td id="sumTDS">0</td>
                    </tr>
                    <tr>
                        <th>Pending Collection</th>
                        <td id="sumPending">₹ 0.00</td>
                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

</form>

</div>
</div>

</div>

@endsection

@section('script')

<script>

$(document).ready(function () {

    loadBooking();

    $('#booking_id').on('change', function () {
        loadBooking();
    });

    $('#invoice_percent').on('keyup change', function () {
        calculateInvoice();
    });

    $('#cgst_percent,#sgst_percent').on('keyup change', function () {
        calculateGST();
    });

    $('#actual_receipt_amount,#bank_received_amount,#tds_amount').on('keyup change', function () {
        updateSummary();
    });

    $('#invoice_type').on('change', function () {

        if ($(this).val() == 'credit_note') {

            $('#creditNoteCard').slideDown();

        } else {

            $('#creditNoteCard').slideUp();

        }

    });

});


/*==================================================
LOAD BOOKING DETAILS
==================================================*/

function loadBooking() {

    let option = $('#booking_id option:selected');

    if(option.val()==''){
        return;
    }
    let agreement = parseFloat(option.data('agreement')) || 0;

    let brokerage = parseFloat(option.data('brokerage')) || 0;

    let used = parseFloat(option.data('used-percent')) || 0;

    let remaining = brokerage - used;

    $('#client_name').val(option.data('client'));

    $('#project_name').val(option.data('project'));
    loadBillingEntities(option.data('developer'));

    loadBankAccounts();
    $('#agreement_value').val(agreement.toFixed(2));

    $('#brokerage_percent').val(brokerage.toFixed(2));

    $('#remaining_percent').val(remaining.toFixed(2));

    $('#sumAgreement').html(formatMoney(agreement));

    $('#sumBrokerage').html(brokerage.toFixed(2)+'%');

    $('#sumRemaining').html(remaining.toFixed(2)+'%');

    calculateInvoice();

}


/*==================================================
INVOICE CALCULATION
==================================================*/

function calculateInvoice(){

    let agreement = parseFloat($('#agreement_value').val()) || 0;

    let remaining = parseFloat($('#remaining_percent').val()) || 0;

    let invoicePercent = parseFloat($('#invoice_percent').val()) || 0;

    if(invoicePercent > remaining){

        alert(
            'Invoice % exceeds remaining brokerage balance.\nRemaining Allowed : '
            + remaining.toFixed(2) + '%'
        );

        $('#invoice_percent').val('');

        $('#invoice_amount').val('');

        calculateGST();

        return;
    }

    let invoiceAmount = (agreement * invoicePercent) / 100;

    $('#invoice_amount').val(invoiceAmount.toFixed(2));

    calculateGST();

}


/*==================================================
GST CALCULATION
==================================================*/

function calculateGST(){

    let invoice = parseFloat($('#invoice_amount').val()) || 0;

    let cgstPercent = parseFloat($('#cgst_percent').val()) || 0;

    let sgstPercent = parseFloat($('#sgst_percent').val()) || 0;

    let cgst = invoice * cgstPercent / 100;

    let sgst = invoice * sgstPercent / 100;

    let gst = cgst + sgst;

    let grand = invoice + gst;

    $('#cgst_amount').val(cgst.toFixed(2));

    $('#sgst_amount').val(sgst.toFixed(2));

    $('#total_gst_amount').val(gst.toFixed(2));

    $('#grand_invoice').val(grand.toFixed(2));

    /*
    Auto Calculation
    */

    let brokerageReceived = invoice * 98 / 100;

    let tds = invoice * 2 / 100;

    let actualReceipt = grand - tds;

    $('#bank_received_amount').val(
        brokerageReceived.toFixed(2)
    );

    $('#tds_amount').val(
        tds.toFixed(2)
    );

    $('#actual_receipt_amount').val(
        actualReceipt.toFixed(2)
    );

    updateSummary();

}


/*==================================================
SUMMARY
==================================================*/

function updateSummary(){

    let agreement = parseFloat($('#agreement_value').val()) || 0;

    let brokerage = parseFloat($('#brokerage_percent').val()) || 0;

    let remaining = parseFloat($('#remaining_percent').val()) || 0;

    let invoice = parseFloat($('#invoice_amount').val()) || 0;

    let gst = parseFloat($('#total_gst_amount').val()) || 0;

    let grand = parseFloat($('#grand_invoice').val()) || 0;

    let receipt = parseFloat($('#actual_receipt_amount').val()) || 0;

    let bank = parseFloat($('#bank_received_amount').val()) || 0;

    let tds = parseFloat($('#tds_amount').val()) || 0;

    let pending = grand - (receipt + tds);

    if(pending < 0){
        pending = 0;
    }

    $('#sumAgreement').html(formatMoney(agreement));

    $('#sumBrokerage').html(brokerage.toFixed(2)+'%');

    $('#sumRemaining').html(remaining.toFixed(2)+'%');

    $('#sumInvoice').html(formatMoney(invoice));

    $('#sumGST').html(formatMoney(gst));

    $('#sumGrand').html(formatMoney(grand));

    $('#sumReceipt').html(formatMoney(receipt));

    $('#sumTDS').html(formatMoney(tds));

    if($('#sumPending').length){

        $('#sumPending').html(formatMoney(pending));

    }

}


/*==================================================
FORMAT MONEY
==================================================*/

function formatMoney(number){

    return '₹ ' + Number(number).toLocaleString(
        'en-IN',
        {
            minimumFractionDigits:2,
            maximumFractionDigits:2
        }
    );

}
function loadBillingEntities(developerId){

    if(!developerId) return;

    $.get('/developer-billing/'+developerId,function(res){

        let html='<option value="">Select</option>';

        $.each(res,function(i,row){

            html += '<option value="'+row.id+'">'+row.entity_name+'</option>';

        });

        $('#developer_billing_entity_id').html(html);

    });

}
function loadBankAccounts(){

    $.get('/company-bank-accounts',function(res){

        let html='<option value="">Select</option>';

        $.each(res,function(i,row){

            html += '<option value="'+row.id+'">'+row.bank_name+' - '+row.account_number+'</option>';

        });

        $('#company_bank_account_id').html(html);

    });

}
</script>

@endsection