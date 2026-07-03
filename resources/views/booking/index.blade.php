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
   <div class="card mb-3">
      <div class="card-body">
         <div class="row">
            <div class="col-md-2">
               <label>Project</label>
               <select id="projectFilter"
                  class="selectpicker form-select"
                  data-live-search="true">
               </select>
            </div>
            <div class="col-md-2">
               <label>Developer</label>
               <select id="developerFilter"
                        class="selectpicker form-select"
                        data-live-search="true">
                </select>
            </div>
            <div class="col-md-2">
               <label>FOS</label>
               <select id="fosFilter" class="selectpicker form-select" data-live-search="true">
               </select>
            </div>
            <div class="col-md-2">
               <label>TL</label>
               <select id="tlFilter"
                  class="selectpicker form-select"
                  data-live-search="true">
               </select>
            </div>
            <div class="col-md-2">
               <label>Sr TL</label>
               <select id="srtlFilter"
                  class="selectpicker form-select"
                  data-live-search="true">
               </select>
            </div>
            <div class="col-md-2">
               <label>CH</label>
               <select id="chFilter"
                  class="selectpicker form-select"
                  data-live-search="true">
               </select>
            </div>
            <div class="col-md-2">
               <label>Booking Status</label>
               <select id="bookingStatusFilter" class="form-select">
                  <option value="">All</option>
                  <option value="approved">Approved</option>
                  <option value="pending">Pending</option>
                  <option value="cancelled">Cancelled</option>
               </select>
            </div>
            <div class="col-md-2">
               <label>Payment Status</label>
               <select id="paymentStatusFilter" class="form-select">
                  <option value="">All</option>
                  <option value="pending">Pending</option>
                  <option value="partial">Partial</option>
                  <option value="completed">Completed</option>
               </select>
            </div>
            <div class="col-md-2">
               <label>Lead Source</label>
               <input type="text"
                  id="leadSourceFilter"
                  class="form-control">
            </div>
         </div>
         <hr>
         <div class="row">
            <div class="col-md-2">
               <label>Booking From</label>
               <input type="date"
                  id="bookingFrom"
                  class="form-control">
            </div>
            <div class="col-md-2">
               <label>Booking To</label>
               <input type="date"
                  id="bookingTo"
                  class="form-control">
            </div>
            <div class="col-md-2">
               <label>Agreement Min</label>
               <input type="number"
                  id="agreementMin"
                  class="form-control">
            </div>
            <div class="col-md-2">
               <label>Agreement Max</label>
               <input type="number"
                  id="agreementMax"
                  class="form-control">
            </div>
            <div class="col-md-2">
               <label>&nbsp;</label>
               <button class="btn btn-primary w-100"
                  id="applyFilter">
               Apply
               </button>
            </div>
            <div class="col-md-2">
               <label>&nbsp;</label>
               <button class="btn btn-success w-100"
                  id="exportBookings">
               Export Excel
               </button>
            </div>
         </div>
      </div>
   </div>
   <div class="card">
      <h5 class="card-header">Bookings</h5>
      <div class="table-responsive">
        <table id="booking-datatable"
            class="table table-striped table-bordered nowrap"
            width="100%">
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

                    <!-- Invoice Summary -->
                    <th>Total Invoice %</th>
                    <th>Total Invoice Amount</th>
                    <th>Total GST</th>
                    <th>Grand Invoice</th>

                    <!-- Collection Summary -->
                    <th>Amount Received from Developer (Incl. GST)</th>
                    <th>Brokerage Received (After TDS)</th>
                    <th>Pending Collection</th>

                    <!-- Brokerage Summary -->
                    <th>Pending %</th>
                    <th>Pending Brokerage</th>

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
            <input type="hidden" name="agreement_value_raw" id="agreement_value_raw">
            <input type="hidden" name="brokerage_amount_raw" id="brokerage_amount_raw">
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
                     <label>Final Revenue(Total Brokerage + Additional Kicker)</label>
                     <input type="text" id="total_brokerage_amount" class="form-control" readonly>
                  </div>
               </div>
               <hr>
               {{-- HISTORY --}}
               <h6>Invoice History</h6>
               <div class="table-responsive">
               <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Type</th>
                        <th>%</th>
                        <th>Invoice</th>
                        <th>GST</th>
                        <th>Grand</th>
                        <th>Amount Received from Developer (Incl. GST)</th>
                        <th>Brokerage Received (After TDS)</th>
                        <th>Pending</th>
                        <th>TDS</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="paymentHistory">
                </tbody>
            </table>
                </div>
               <hr>
               <h6>Add New Invoice</h6>
               <div class="row">
                  {{-- Invoice Details --}}
                  <div class="col-md-4 mb-3">
                     <label>Invoice Number</label>
                     <input type="text"
                        name="invoice_number"
                        class="form-control">
                  </div>
                  <div class="col-md-4 mb-3">
                     <label>Invoice Type</label>
                     <select name="invoice_type" class="form-control">
                        <option value="tax_invoice">Tax Invoice</option>
                        <option value="proforma">Proforma Invoice</option>
                        <option value="credit_note">Credit Note</option>
                     </select>
                  </div>
                  <div class="col-md-4 mb-3">
                     <label>Invoice Date</label>
                     <input type="date"
                        name="invoice_date" required
                        class="form-control">
                  </div>
                  {{-- Brokerage --}}
                  <div class="col-md-6 mb-3">
                     <label>Invoice %</label>
                     <input type="number"
                        name="invoice_percent"
                        id="invoice_percent"
                        class="form-control"
                        step="0.01">
                  </div>
                  <div class="col-md-6 mb-3">
                     <label>Invoice Amount (Before GST)</label>
                     <input type="number"
                        name="invoice_amount"
                        id="invoice_amount"
                        class="form-control"
                        step="0.01"
                        >
                  </div>
                  <div class="col-md-3 mb-3">
                     <label>CGST %</label>
                     <input type="number"
                        name="cgst_percent"
                        id="cgst_percent"
                        value="9"
                        readonly
                        class="form-control">
                  </div>
                  <div class="col-md-3 mb-3">
                     <label>CGST Amount</label>
                     <input type="number"
                        name="cgst_amount"
                        id="cgst_amount"
                        readonly
                        class="form-control">
                  </div>
                  <div class="col-md-3 mb-3">
                     <label>SGST %</label>
                     <input type="number"
                        name="sgst_percent"
                        id="sgst_percent"
                        value="9"
                        readonly
                        class="form-control">
                  </div>
                  <div class="col-md-3 mb-3">
                     <label>SGST Amount</label>
                     <input type="number"
                        name="sgst_amount"
                        id="sgst_amount"
                        readonly
                        class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                     <label>Total GST Amount</label>
                     <input type="number"
                        name="total_gst_amount"
                        id="total_gst_amount"
                        readonly
                        class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                     <label>Grand Invoice Value</label>
                     <input type="number"
                        name="grand_invoice_amount"
                        id="grand_invoice_amount"
                        step="0.01"
                        readonly
                        class="form-control">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label>Amount Received from Developer (Incl. GST)</label>
                    <input type="number"
                        name="actual_receipt_amount"
                        id="actual_receipt_amount"
                        class="form-control" step="0.01"
                        >
                </div>
                  <div class="col-md-6 mb-3">
                     <label>Developer Billing Entity</label>
                     <select name="developer_billing_entity_id" 
                        class="form-control">
                        <option value="">Select Billing Entity</option>
                        @foreach($developerBillingEntities as $entity)
                        <option value="{{ $entity->id }}">
                           {{ $entity->entity_name }}
                        </option>
                        @endforeach
                     </select>
                  </div>
                  {{-- Bank --}}
                  <div class="col-md-6 mb-3">
                     <label>Company Bank Account</label>
                     <select name="company_bank_account_id"
                        class="form-control">
                        <option value="">Select Bank</option>
                        @foreach($companyBanks as $bank)
                        <option value="{{ $bank->id }}">
                           {{ $bank->account_name }}
                           - {{ $bank->bank_name }}
                        </option>
                        @endforeach
                     </select>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label>Invoice File</label>
                     <input type="file"
                        name="invoice_file"
                        accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                        class="form-control">
                  </div>
                  {{-- Payment --}}
                  <div class="col-md-6 mb-3">
                     <label>Brokerage Received (After TDS)</label>
                     <input type="number"
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
                     <input type="date"
                        name="bank_received_date"
                        class="form-control">
                  </div>
                  <div class="col-md-6 mb-3 credit-note-fields d-none">
                        <label>Credit Note Number</label>
                        <input type="text"
                            name="credit_note_number"
                            class="form-control">
                    </div>

                    <div class="col-md-6 mb-3 credit-note-fields d-none">
                        <label>Credit Note Date</label>
                        <input type="date"
                            name="credit_note_date"
                            class="form-control">
                    </div>

                    <div class="col-md-12 mb-3 credit-note-fields d-none">
                        <label>Credit Note Reason</label>
                        <textarea name="credit_note_reason"
                                class="form-control"></textarea>
                    </div>
                  <div class="col-md-12 mb-3">
                     <label>Remarks</label>
                     <textarea name="remarks"
                        class="form-control"></textarea>
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
                    <label>Invoice Number</label>
                    <input type="text"
                        name="invoice_number"
                        id="edit_invoice_number"
                        class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>Invoice Type</label>
                    <select name="invoice_type"
                        id="edit_invoice_type"
                        class="form-select">
                        <option value="tax_invoice">Tax Invoice</option>
                        <option value="proforma">Proforma Invoice</option>
                        <option value="credit_note">Credit Note</option>
                    </select>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>Developer Billing Entity</label>
                    <select
                        name="developer_billing_entity_id"
                        id="edit_developer_billing_entity_id"
                        class="selectpicker form-select"
                        data-live-search="true">
                        <option value="">Select Entity</option>
                        @foreach($developerBillingEntities as $entity)
                        <option value="{{ $entity->id }}">
                            {{ $entity->entity_name }}
                        </option>
                        @endforeach
                    </select>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>Company Bank Account</label>
                    <select name="company_bank_account_id"
                        id="edit_company_bank_account_id"
                        class="form-select">
                        <option value="">Select Bank</option>
                        @foreach($companyBanks as $bank)
                        <option value="{{ $bank->id }}">
                            {{ $bank->account_name }}
                            -
                            {{ $bank->bank_name }}
                        </option>
                        @endforeach
                    </select>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>Invoice %</label>
                    <input type="number"
                        step="0.01"
                        name="invoice_percent"
                        id="edit_invoice_percent"
                        class="form-control">
                    <input type="hidden" id="edit_booking_agreement_value">
                    <input type="hidden" id="edit_total_brokerage_percent">
                    <input type="hidden" id="edit_current_invoice_percent">
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>Invoice Amount</label>
                    <input type="number"
                        step="0.01"
                        name="invoice_amount"
                        id="edit_invoice_amount"
                        class="form-control"
                        step="0.01"
                        readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>CGST %</label>
                    <input type="number"
                        step="0.01"
                        name="cgst_percent"
                        id="edit_cgst_percent"
                        class="form-control"
                        readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>CGST Amount</label>
                    <input type="number"
                        step="0.01"
                        name="cgst_amount"
                        id="edit_cgst_amount"
                        class="form-control"
                        readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>SGST %</label>
                    <input type="number"
                        step="0.01"
                        name="sgst_percent"
                        id="edit_sgst_percent"
                        class="form-control"
                        readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>SGST Amount</label>
                    <input type="number"
                        step="0.01"
                        name="sgst_amount"
                        id="edit_sgst_amount"
                        class="form-control"
                        readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>Total GST Amount</label>
                    <input type="number"
                        step="0.01"
                        name="total_gst_amount"
                        id="edit_total_gst_amount"
                        class="form-control"
                        readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Grand Invoice Value</label>
                        <input type="number"
                            id="edit_grand_invoice_amount"
                            readonly
                            class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Amount Received from Developer (Incl. GST)</label>
                        <input type="number"
                            name="actual_receipt_amount"
                            id="edit_actual_receipt_amount"
                            class="form-control" step="0.01"
                            >
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
                        <div id="existingInvoiceFile"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>Brokerage Received (After TDS)</label>
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
                        class="form-control">
                    </div>
                    <div class="col-md-6 mb-3 credit-note-fields d-none">
                    <label>Credit Note Date</label>
                    <input type="date"
                        name="credit_note_date"
                        id="edit_credit_note_date"
                        class="form-control">
                    </div>
                    <div class="col-md-6 mb-3 credit-note-fields d-none">
                        <label>Credit Note Number</label>
                        <input type="text"
                            name="credit_note_number"
                            id="edit_credit_note_number"
                            class="form-control">
                    </div>
                    <div class="col-md-12 mb-3 credit-note-fields d-none">
                    <label>Credit Note Reason</label>
                    <textarea name="credit_note_reason"
                        id="edit_credit_note_reason"
                        class="form-control"></textarea>
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

    $(document).ready(function () {

        loadUsersByRole('fos', '#fosFilter');
        loadUsersByRole('tl', '#tlFilter');
        loadUsersByRole('srtl', '#srtlFilter');
        loadUsersByRole('ch', '#chFilter');

        $('.selectpicker').selectpicker();

        /* ================= DATATABLE ================= */

        if ($.fn.DataTable.isDataTable('#booking-datatable')) {
            $('#booking-datatable').DataTable().destroy();
        }

        let table = $('#booking-datatable').DataTable({

            processing: true,
            serverSide: true,
            scrollX: true,
            responsive: false,
            order: [[0, 'desc']],

            ajax: {
                url: "{{ route('booking.index') }}",

                data: function (d) {

                    d.project_id = $('#projectFilter').val() || '';
                    d.developer_id = $('#developerFilter').val() || '';

                    d.fos_id = $('#fosFilter').val() || '';
                    d.tl_id = $('#tlFilter').val() || '';
                    d.srtl_id = $('#srtlFilter').val() || '';
                    d.ch_id = $('#chFilter').val() || '';

                    d.booking_status = $('#bookingStatusFilter').val() || '';
                    d.payment_status = $('#paymentStatusFilter').val() || '';

                    d.lead_source = $('#leadSourceFilter').val() || '';

                    d.booking_from = $('#bookingFrom').val() || '';
                    d.booking_to = $('#bookingTo').val() || '';

                    d.agreement_min = $('#agreementMin').val() || '';
                    d.agreement_max = $('#agreementMax').val() || '';
                }
            },

            columns: [

                { data: 'id' },
                { data: 'booking_date' },
                { data: 'client_name' },
                { data: 'client_contact' },
                { data: 'lead_source' },

                { data: 'project_name' },
                { data: 'developer_name' },

                { data: 'booking_amount' },
                { data: 'agreement_value' },

                { data: 'total_brokerage_percent' },
                { data: 'current_effective_amount' },
                { data: 'final_revenue' },

                {
                    data: 'team_hierarchy',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'booking_confirm',
                    orderable: false,
                    searchable: false
                },

                { data: 'total_invoice_percent' },
                { data: 'total_invoice_amount' },
                { data: 'total_gst_amount' },
                { data: 'total_grand_invoice_amount' },

                { data: 'total_actual_receipt_amount' },
                { data: 'total_received_amount' },
                { data: 'pending_collection_amount' },

                { data: 'pending_brokerage_percent' },
                { data: 'pending_brokerage_amount' },

                { data: 'payment_status' },

                {
                    data: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        /* ================= APPLY FILTER ================= */

        $('#applyFilter').click(function () {
            table.ajax.reload();
        });

    });
   
  /* ================= OPEN PAYMENT MODAL ================= */

    $(document).on('click', '.add-payment', function () {

        /* ================= RESET FORM ================= */

        $('#invoice_percent').val('');
        $('#invoice_amount').val('');

        $('#cgst_amount').val('');
        $('#sgst_amount').val('');
        $('#total_gst_amount').val('');
        $('#grand_invoice_amount').val('');

        $('#addPaymentModal input[name="invoice_date"]').val('');
        $('#addPaymentModal input[name="bank_received_amount"]').val('');
        $('#addPaymentModal input[name="bank_received_date"]').val('');
        $('#addPaymentModal input[name="tds_amount"]').val('');

        $('#addPaymentModal textarea[name="remarks"]').val('');

        $('#addPaymentModal select[name="invoice_type"]')
            .val('tax_invoice');

        $('#addPaymentModal select[name="developer_billing_entity_id"]')
            .val('');

        $('#addPaymentModal select[name="company_bank_account_id"]')
            .val('');

        totalInvoiceUsed = 0;

        let booking_id = $(this).data('id');
        let agreement = $(this).data('agreement');
        let percent = $(this).data('percent');
        let brokerage = $(this).data('brokerage');
        let status = $(this).data('status');

        $('#booking_id').val(booking_id);

        $('#agreement_value').val(
            '₹ ' + Number(agreement).toLocaleString()
        );

        $('#total_brokerage_percent').val(
            percent + '%'
        );

        $('#total_brokerage_amount').val(
            '₹ ' + Number(brokerage).toLocaleString()
        );

        $('#agreement_value_raw').val(agreement);
        $('#brokerage_amount_raw').val(brokerage);

        /* ================= HISTORY AJAX ================= */

        $('#paymentHistory').html(
            '<tr><td colspan="6" class="text-center">Loading...</td></tr>'
        );

        $.get(
            "{{ url('booking/payment-history') }}/" + booking_id,
            function (data) {

                let html = '';

                if (data.length > 0) {

                    let totalPercent = 0;
                    let totalInvoice = 0;
                    let totalGST = 0;
                    let totalGrand = 0;
                    let totalActualReceipt = 0;
                    let totalBankReceipt = 0;
                    let totalPending = 0;
                    let totalTds = 0;

                    data.forEach(function (p) {

                        totalPercent += parseFloat(p.invoice_percent || 0);

                        let invoice = Number(p.invoice_amount || 0);
                        let gst = Number(p.total_gst_amount || 0);
                        let grand = invoice + gst;

                        let actualReceipt = Number(p.actual_receipt_amount || 0);
                        let bankReceipt = Number(p.bank_received_amount || 0);
                        let tds = Number(p.tds_amount || 0);

                        // Pending after considering TDS deducted by client
                        let pending = grand - (actualReceipt + tds);

                        if (pending < 0) {
                            pending = 0;
                        }

                        totalInvoice += invoice;
                        totalGST += gst;
                        totalGrand += grand;
                        totalActualReceipt += actualReceipt;
                        totalBankReceipt += bankReceipt;
                        totalPending += pending;
                        totalTds += tds;

                        totalInvoiceUsed += parseFloat(
                            p.invoice_percent || 0
                        );

                        let badge = '';

                        switch (p.status) {

                            case 'received':
                                badge = '<span class="badge bg-success">Received</span>';
                                break;

                            case 'partial':
                                badge = '<span class="badge bg-info">Partial</span>';
                                break;

                            default:
                                badge = '<span class="badge bg-warning">Invoice Raised</span>';
                        }

                        let fileBtn = '';

                        if (p.invoice_file) {

                            fileBtn = `
                                <a href="{{ asset('storage') }}/${p.invoice_file}"
                                    target="_blank"
                                    class="btn btn-sm btn-info">
                                    View File
                                </a>
                            `;
                        }

                        let actionBtn = `
                            <button type="button"
                                class="btn btn-sm btn-primary update-payment"

                                data-id="${p.id}"
                                data-agreement="${agreement}"
                                data-total_brokerage_percent="${percent}"
                                data-old_invoice_percent="${p.invoice_percent || 0}"

                                data-invoice_number="${p.invoice_number || ''}"
                                data-invoice_type="${p.invoice_type || 'invoice'}"

                                data-developer_billing_entity_id="${p.developer_billing_entity_id || ''}"
                                data-company_bank_account_id="${p.company_bank_account_id || ''}"

                                data-invoice_percent="${p.invoice_percent || ''}"
                                data-booking_agreement="${agreement}"
                                data-invoice_amount="${p.invoice_amount || ''}"

                                data-cgst_percent="${p.cgst_percent || 9}"
                                data-cgst_amount="${p.cgst_amount || ''}"

                                data-sgst_percent="${p.sgst_percent || 9}"
                                data-sgst_amount="${p.sgst_amount || ''}"

                                data-total_gst_amount="${p.total_gst_amount || ''}"

                                data-invoice_date="${p.invoice_date || ''}"

                                data-bank_received_amount="${p.bank_received_amount || ''}"
                                data-actual_receipt_amount="${p.actual_receipt_amount || ''}"
                                data-bank_received_date="${p.bank_received_date || ''}"

                                data-tds_amount="${p.tds_amount || ''}"

                                data-credit_note_date="${p.credit_note_date || ''}"
                                data-credit_note_reason="${p.credit_note_reason || ''}"
                                data-credit_note_number="${p.credit_note_number || ''}"
                                data-status="${p.status || ''}"
                                data-remarks="${p.remarks || ''}"
                                data-invoice_file="${p.invoice_file || ''}">
                                Edit
                            </button>
                        `;

                        html += `
                            <tr>

                                <td>${p.invoice_number ?? '-'}</td>

                                <td>${p.invoice_type ?? '-'}</td>

                                <td>
                                    ${
                                        parseFloat(p.invoice_percent || 0) > 0
                                        ? p.invoice_percent + '%'
                                        : 'Additional'
                                    }
                                </td>

                                <td>
                                    ₹ ${invoice.toLocaleString()}
                                </td>

                                <td>
                                    ₹ ${gst.toLocaleString()}
                                </td>

                                <td>
                                    <strong>₹ ${grand.toLocaleString()}</strong>
                                </td>

                                <td>
                                    ₹ ${actualReceipt.toLocaleString()}
                                </td>

                                <td>
                                    ₹ ${bankReceipt.toLocaleString()}
                                </td>

                                <td>
                                    <span class="${
                                        pending > 0
                                            ? 'text-danger fw-bold'
                                            : 'text-success fw-bold'
                                    }">
                                        ₹ ${pending.toLocaleString()}
                                    </span>
                                </td>

                                <td>
                                    ₹ ${tds.toLocaleString()}
                                </td>

                                <td>
                                    ${badge}
                                </td>

                                <td>
                                    ${p.invoice_date || '-'}
                                    <br>
                                    ${fileBtn}
                                </td>

                                <td>
                                    ${actionBtn}
                                </td>

                            </tr>
                        `;
                    });

                    html += `
                        <tr class="table-dark fw-bold">

                            <td colspan="2" class="text-center">
                                Total
                            </td>

                            <td>${totalPercent.toFixed(2)}%</td>

                            <td>₹ ${totalInvoice.toLocaleString()}</td>

                            <td>₹ ${totalGST.toLocaleString()}</td>

                            <td>₹ ${totalGrand.toLocaleString()}</td>

                            <td>₹ ${totalActualReceipt.toLocaleString()}</td>

                            <td>₹ ${totalBankReceipt.toLocaleString()}</td>

                            <td>
                                <span class="text-warning">
                                    ₹ ${totalPending.toLocaleString()}
                                </span>
                            </td>

                            <td>₹ ${totalTds.toLocaleString()}</td>

                            <td colspan="3"></td>

                        </tr>
                    `;

                } else {

                    html =
                        '<tr><td colspan="13" class="text-center">No history found</td></tr>';
                }

                $('#paymentHistory').html(html);
            }
        ).fail(function () {

            $('#paymentHistory').html(
                '<tr><td colspan="13" class="text-center text-danger">Failed to load payment history.</td></tr>'
            );

        });
        /* ================= DISABLE IF COMPLETED ================= */

        // if (status === 'completed') {

        //     $('#addPaymentModal')
        //         .find('input, textarea, select')
        //         .prop('disabled', true);

        //     $('#savePaymentBtn').hide();

        // } else {

        //     $('#addPaymentModal')
        //         .find('input, textarea, select')
        //         .prop('disabled', false);

        //     $('#savePaymentBtn').show();
        // }
        $('#addPaymentModal')
        .find('input, textarea, select')
        .prop('disabled', false);

        $('#savePaymentBtn').show();
        $('#addPaymentModal').modal('show');

    });
   
   
  /* ================= UPDATE PAYMENT ================= */

    $(document).on('click', '.update-payment', function () {

        let id = $(this).data('id');

        $('#receiveForm').attr(
            'action',
            "{{ url('booking/payment-update') }}/" + id
        );

        $('#edit_invoice_number').val(
            $(this).data('invoice_number')
        );

        $('#edit_invoice_type').val(
            $(this).data('invoice_type')
        );

        let entityId = String(
            $(this).data('developer_billing_entity_id') || ''
        );

        let bankId = String(
            $(this).data('company_bank_account_id') || ''
        );

        $('#edit_developer_billing_entity_id')
            .selectpicker('val', entityId);

        $('#edit_company_bank_account_id')
            .val(bankId)
            .trigger('change');

        $('#edit_invoice_percent').val(
            $(this).data('invoice_percent')
        );

        $('#edit_booking_agreement_value').val(
            $(this).data('booking_agreement')
        );

        $('#edit_total_brokerage_percent').val(
            $(this).data('total_brokerage_percent')
        );

        $('#edit_current_invoice_percent').val(
            $(this).data('invoice_percent')
        );

        $('#edit_invoice_amount').val(
            $(this).data('invoice_amount')
        );

        $('#edit_cgst_percent').val(
            $(this).data('cgst_percent')
        );

        $('#edit_cgst_amount').val(
            $(this).data('cgst_amount')
        );

        $('#edit_sgst_percent').val(
            $(this).data('sgst_percent')
        );

        $('#edit_sgst_amount').val(
            $(this).data('sgst_amount')
        );

        $('#edit_total_gst_amount').val(
            $(this).data('total_gst_amount')
        );

        let invoiceAmount =
            parseFloat($(this).data('invoice_amount')) || 0;
        let cgstAmount =
            parseFloat($(this).data('cgst_amount')) || 0;

        let sgstAmount =
            parseFloat($(this).data('sgst_amount')) || 0;
        let gstAmount =
            parseFloat($(this).data('total_gst_amount')) || 0;
        if(cgstAmount == 0 && sgstAmount == 0 && invoiceAmount > 0){

            cgstAmount = invoiceAmount * 0.09;
            sgstAmount = invoiceAmount * 0.09;

            $('#edit_cgst_percent').val(9);
            $('#edit_sgst_percent').val(9);

            $('#edit_cgst_amount').val(cgstAmount.toFixed(2));
            $('#edit_sgst_amount').val(sgstAmount.toFixed(2));

            $('#edit_total_gst_amount').val(
                (cgstAmount + sgstAmount).toFixed(2)
            );
        }
        $('#edit_grand_invoice_amount').val(
            (invoiceAmount + gstAmount).toFixed(2)
        );

        $('#edit_invoice_date').val(
            $(this).data('invoice_date')
        );

        $('#edit_received_amount').val(
            $(this).data('bank_received_amount')
        );
        $('#edit_actual_receipt_amount').val(
            $(this).data('actual_receipt_amount')
        );

        $('#edit_received_date').val(
            $(this).data('bank_received_date')
        );

        $('#edit_tds_amount').val(
            $(this).data('tds_amount')
        );

        $('#edit_credit_note_date').val(
            $(this).data('credit_note_date')
        );

        $('#edit_credit_note_reason').val(
            $(this).data('credit_note_reason')
        );

        $('#edit_remarks').val(
            $(this).data('remarks')
        );
        let invoiceFile = $(this).attr('data-invoice_file');

        if(invoiceFile){

            $('#existingInvoiceFile').html(`
                <a href="{{ asset('storage') }}/${invoiceFile}"
                target="_blank"
                class="btn btn-info btn-sm mt-2">
                View Existing Invoice
                </a>
            `);

        }else{

            $('#existingInvoiceFile').html(
                '<span class="text-muted">No file uploaded</span>'
            );
        }

        if ($('#edit_invoice_type').val() === 'credit_note') {
            $('.credit-note-fields').removeClass('d-none');
        } else {
            $('.credit-note-fields').addClass('d-none');
        }

        $('#receivePaymentModal').modal('show');

    });

    $(document).on('change', 'select[name="invoice_type"]', function () {

        let type = $(this).val();

        if (type === 'proforma') {

            $('#cgst_percent').val(0);
            $('#sgst_percent').val(0);

        } else {

            $('#cgst_percent').val(9);
            $('#sgst_percent').val(9);

        }

        calculateGST();

    });
    $(document).on('change', '#edit_invoice_type', function () {

        let type = $(this).val();

        if (type === 'proforma') {

            $('#edit_cgst_percent').val(0);
            $('#edit_sgst_percent').val(0);

        } else {

            $('#edit_cgst_percent').val(9);
            $('#edit_sgst_percent').val(9);

        }

        calculateEditGST();

    });
    /* ================= CREDIT NOTE TOGGLE ================= */

    $(document).on('change', '#edit_invoice_type', function () {

        if ($(this).val() === 'credit_note') {
            $('.credit-note-fields').removeClass('d-none');
        } else {
            $('.credit-note-fields').addClass('d-none');
        }

    });

   /* =====================================================
    | EDIT PAYMENT CALCULATION
    ===================================================== */

    $(document).on('keyup change', '#edit_invoice_percent', function () {

        let percent =
            parseFloat($(this).val()) || 0;

        let agreement =
            parseFloat($('#edit_booking_agreement_value').val()) || 0;

        let totalBrokeragePercent =
            parseFloat($('#edit_total_brokerage_percent').val()) || 0;

        let currentInvoicePercent =
            parseFloat($('#edit_current_invoice_percent').val()) || 0;

        let remainingAllowed =
            totalBrokeragePercent -
            (totalInvoiceUsed - currentInvoicePercent);

        if (percent > remainingAllowed) {

            alert(
                'Invoice % exceeds brokerage balance.\nRemaining Allowed: '
                + remainingAllowed.toFixed(2) + '%'
            );

            $(this).val(currentInvoicePercent);

            percent = currentInvoicePercent;
        }

        let amount = 0;

        if (percent > 0) {
            amount = (agreement * percent) / 100;
        }

        $('#edit_invoice_amount').val(
            amount.toFixed(2)
        );

        calculateEditGST();

    });

    $(document).on('keyup change', '#invoice_percent', function () {

    let percent =
        parseFloat($(this).val()) || 0;

    let agreement =
        parseFloat($('#agreement_value_raw').val()) || 0;

    let totalBrokeragePercent =
        parseFloat(
            $('#total_brokerage_percent')
                .val()
                .replace('%', '')
        ) || 0;

    let remainingAllowed =
        totalBrokeragePercent - totalInvoiceUsed;

    console.log({
        totalBrokeragePercent,
        totalInvoiceUsed,
        remainingAllowed,
        enteredPercent: percent
    });

    if (percent > remainingAllowed) {

        alert(
            'Invoice % exceeds brokerage balance.\nRemaining Allowed: '
            + remainingAllowed.toFixed(2) + '%'
        );

        $(this).val('');

        $('#invoice_amount').val('');

        calculateGST();

        return;
    }

    let amount =
        (agreement * percent) / 100;

    $('#invoice_amount').val(
        amount.toFixed(2)
    );

    calculateGST();
});
    function calculateGST() {

        let invoiceAmount =
            parseFloat($('#invoice_amount').val()) || 0;

        let cgstPercent =
            parseFloat($('#cgst_percent').val()) || 0;

        let sgstPercent =
            parseFloat($('#sgst_percent').val()) || 0;

        let cgstAmount =
            invoiceAmount * cgstPercent / 100;

        let sgstAmount =
            invoiceAmount * sgstPercent / 100;

        let totalGST =
            cgstAmount + sgstAmount;

        let grandTotal =
            invoiceAmount + totalGST;

        $('#cgst_amount').val(cgstAmount.toFixed(2));
        $('#sgst_amount').val(sgstAmount.toFixed(2));
        $('#total_gst_amount').val(totalGST.toFixed(2));
        $('#grand_invoice_amount').val(grandTotal.toFixed(2));

        // TDS
        let tds = invoiceAmount * 0.02;

        // Amount received from developer (after TDS deduction)
        let actualReceipt = grandTotal - tds;

        // Brokerage received after TDS
        let bankReceipt = invoiceAmount - tds;

        $('#actual_receipt_amount').val(actualReceipt.toFixed(2));

        $('input[name="tds_amount"]').val(tds.toFixed(2));

        $('input[name="bank_received_amount"]').val(bankReceipt.toFixed(2));
    }
    $(document).on(
        'keyup change',
        '#invoice_amount,#cgst_percent,#sgst_percent',
        calculateGST
    );
    function calculateEditGST() {

        let invoiceAmount =
            parseFloat($('#edit_invoice_amount').val()) || 0;

        let cgstPercent =
            parseFloat($('#edit_cgst_percent').val()) || 0;

        let sgstPercent =
            parseFloat($('#edit_sgst_percent').val()) || 0;

        let cgstAmount =
            invoiceAmount * cgstPercent / 100;

        let sgstAmount =
            invoiceAmount * sgstPercent / 100;

        let totalGST =
            cgstAmount + sgstAmount;

        let grandTotal =
            invoiceAmount + totalGST;

        $('#edit_cgst_amount').val(cgstAmount.toFixed(2));
        $('#edit_sgst_amount').val(sgstAmount.toFixed(2));
        $('#edit_total_gst_amount').val(totalGST.toFixed(2));
        $('#edit_grand_invoice_amount').val(grandTotal.toFixed(2));

        // TDS
        let tds = invoiceAmount * 0.02;

        // Amount received from developer (after TDS deduction)
        let actualReceipt = grandTotal - tds;

        // Brokerage received after TDS
        let bankReceipt = invoiceAmount - tds;

        $('#edit_actual_receipt_amount').val(actualReceipt.toFixed(2));
        $('#edit_tds_amount').val(tds.toFixed(2));
        $('#edit_received_amount').val(bankReceipt.toFixed(2));
    }


    /* Recalculate GST if amount or GST % changes */

    $(document).on(
        'keyup change',
        '#edit_invoice_amount,#edit_cgst_percent,#edit_sgst_percent',
        calculateEditGST
    );
   /* ================= TOOLTIP FIX ================= */

    $(document).on('draw.dt', function () {
        $('[data-bs-toggle="tooltip"]').tooltip({
            html: true
        });
    });


    /* ================= LOAD PROJECTS ================= */

    $.get("{{ route('booking.projects') }}", function (data) {

        console.log('Projects =>', data);

        let $el = $('#projectFilter');

        $el.empty().append('<option value="">All</option>');

        data.forEach(function (item) {
            $el.append(
                `<option value="${item.id}">${item.name}</option>`
            );
        });

        if ($el.hasClass('selectpicker')) {
            $el.selectpicker('refresh');
        }

    }).fail(function (xhr) {

        console.log(
            'Projects Error =>',
            xhr.responseText
        );

    });


    /* ================= LOAD DEVELOPERS ================= */

    $.get("{{ route('booking.developers') }}", function (data) {

        console.log('Developers =>', data);

        let $el = $('#developerFilter');

        $el.empty().append('<option value="">All</option>');

        data.forEach(function (item) {

            $el.append(
                `<option value="${item.id}">
                    ${item.name}
                </option>`
            );

        });

        if ($el.hasClass('selectpicker')) {
            $el.selectpicker('refresh');
        }

    }).fail(function (xhr) {

        console.log(
            'Developers Error =>',
            xhr.responseText
        );

    });



    /* ================= UPDATE BOOKING STATUS ================= */

    function updateBStatus(el, bookingId) {

        let status = el.value;

        fetch(
            "{{ route('booking.update_bstatus') }}",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    id: bookingId,
                    booking_confirm: status
                })
            }
        )
        .then(res => res.json())
        .then(data => {

            $('#statusMsg').html(`
                <div class="alert alert-success alert-dismissible fade show">
                    ${data.message}
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            `);

            setTimeout(function () {
                $('#statusMsg').html('');
            }, 3000);

        })
        .catch(function (err) {
            console.error(err);
        });
    }


    /* ================= APPLY FILTER ================= */

    $('#applyFilter').on('click', function () {

        $('#booking-datatable')
            .DataTable()
            .ajax
            .reload(null, false);

    });


    /* ================= LOAD USERS BY ROLE ================= */

    function loadUsersByRole(role, selector) {

        $.get(
            "{{ route('booking.users-by-role') }}",
            {
                role: role
            },
            function (data) {

                let $el = $(selector);

                $el.empty().append(
                    '<option value="">All</option>'
                );

                data.forEach(function (item) {

                    $el.append(
                        `<option value="${item.id}">
                            ${item.name}
                        </option>`
                    );

                });

                if ($el.hasClass('selectpicker')) {
                    $el.selectpicker('refresh');
                }
            }
        );
    }


    /* ================= EXPORT BOOKINGS ================= */

    $('#exportBookings').on('click', function () {

        let params = $.param({

            project_id:
                $('#projectFilter').val(),

            developer_id:
                $('#developerFilter').val(),

            fos_id:
                $('#fosFilter').val(),

            tl_id:
                $('#tlFilter').val(),

            srtl_id:
                $('#srtlFilter').val(),

            ch_id:
                $('#chFilter').val(),

            booking_status:
                $('#bookingStatusFilter').val(),

            payment_status:
                $('#paymentStatusFilter').val(),

            lead_source:
                $('#leadSourceFilter').val(),

            booking_from:
                $('#bookingFrom').val(),

            booking_to:
                $('#bookingTo').val(),

            agreement_min:
                $('#agreementMin').val(),

            agreement_max:
                $('#agreementMax').val()
        });

        window.location =
            "{{ route('booking.export') }}?" + params;

    });
   
</script>
@endsection