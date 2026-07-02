@extends('layouts.app')
@section('content')
<style>
    .card-body {
        margin-top: 20px;
    }
   .timeline{
   position:relative;
   margin-left:20px;
   }
   .timeline:before{
   content:'';
   position:absolute;
   left:11px;
   top:0;
   bottom:0;
   width:2px;
   background:#d9d9d9;
   }
   .timeline-item{
   position:relative;
   margin-bottom:35px;
   padding-left:40px;
   }
   .timeline-marker{
   position:absolute;
   left:0;
   width:24px;
   height:24px;
   border-radius:50%;
   }
   .timeline-content{
   background:#fff;
   border:1px solid #eee;
   padding:15px;
   border-radius:8px;
   }
   .table th{
   width: 35%;
   background: #f7f7f7;
   font-weight: 600;
   }
   .card{
   border-radius: 12px;
   box-shadow: 0 2px 10px rgba(0,0,0,0.04);
   }
   .badge{
   font-size: 12px;
   padding: 7px 10px;
   }
   .summary-card{
   transition: 0.2s ease;
   }
   .summary-card:hover{
   transform: translateY(-2px);
   }
   .info-title{
   font-size: 12px;
   color: #8592a3;
   margin-bottom: 5px;
   }
   .info-value{
   font-size: 15px;
   font-weight: 600;
   }
   .section-title{
   font-size: 16px;
   font-weight: 700;
   }
   .table td{
   vertical-align: middle;
   }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
   {{-- HEADER --}}
   <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
         <h4 class="fw-bold mb-1">
            Booking #{{ $booking->id }}
         </h4>
         <div class="mt-1">
            {{-- BOOKING STATUS --}}
            @if($booking->booking_confirm == 'approved')
            <span class="badge bg-success">Approved</span>
            @elseif($booking->booking_confirm == 'cancelled')
            <span class="badge bg-danger">Cancelled</span>
            @else
            <span class="badge bg-warning">Pending</span>
            @endif
            {{-- PAYMENT STATUS --}}
            @if($booking->payment_status == 'completed')
            <span class="badge bg-success">Completed</span>
            @elseif($booking->payment_status == 'partial')
            <span class="badge bg-warning">Partial</span>
            @else
            <span class="badge bg-danger">Pending</span>
            @endif
         </div>
      </div>
      <div>
         @if(!isset($pdf))
         <a href="#"
            class="btn btn-success">
         <i class="bx bx-money"></i>
         Add Invoice
         </a>
         <a href="{{ route('booking.pdf', $booking->id) }}"
            class="btn btn-primary">
         <i class="bx bx-download"></i>
         Download PDF
         </a>
         <a href="{{ route('booking.index') }}"
            class="btn btn-secondary">
         Back
         </a>
         <a href="{{ route('booking.edit',$booking->id) }}"
            class="btn btn-primary">
         Edit Booking
         </a>
         @endif
      </div>
   </div>
   {{-- ================= PAGE INFO BAR ================= --}}
   <div class="card mb-4">
      <div class="row mb-4">
         <div class="col-lg-3 col-md-6">
            <div class="card border-start border-primary border-4">
               <div class="card-body">
                  <small class="text-muted">Agreement Value</small>
                  <h4 class="mt-2 mb-0 text-primary">
                     ₹ {{ number_format($booking->agreement_value ?? 0,2) }}
                  </h4>
               </div>
            </div>
         </div>
         <div class="col-lg-3 col-md-6">
            <div class="card border-start border-success border-4">
               <div class="card-body">
                  <small class="text-muted">Final Revenue</small>
                  <h4 class="mt-2 mb-0 text-success">
                     ₹ {{ number_format($booking->final_revenue ?? 0,2) }}
                  </h4>
               </div>
            </div>
         </div>
         <div class="col-lg-3 col-md-6">
            <div class="card border-start border-info border-4">
               <div class="card-body">
                  <small class="text-muted">Grand Invoice</small>
                  <h4 class="mt-2 mb-0 text-info">
                     ₹ {{ number_format($booking->total_grand_invoice_amount ?? 0,2) }}
                  </h4>
               </div>
            </div>
         </div>
         <div class="col-lg-3 col-md-6">
            <div class="card border-start border-danger border-4">
               <div class="card-body">
                  <small class="text-muted">Pending Brokerage</small>
                  <h4 class="mt-2 mb-0 text-danger">
                     ₹ {{ number_format($booking->pending_brokerage_amount ?? 0,2) }}
                  </h4>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="card mt-4">
        <div class="card-header bg-label-primary">
         <h5 class="mb-0">
            <i class="bx bx-detail"></i>
            Booking Details
         </h5>
      </div>
      <div class="card-body">
         <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
               <table class="table table-bordered table-sm">
                  <tr>
                     <th width="40%">Booking ID</th>
                     <td>#{{ $booking->id }}</td>
                  </tr>
                  <tr>
                     <th>Booking Date</th>
                     <td>{{ $booking->booking_date }}</td>
                  </tr>
                  <tr>
                     <th>Booking Status</th>
                     <td>
                        @if($booking->booking_confirm=='approved')
                        <span class="badge bg-success">Approved</span>
                        @elseif($booking->booking_confirm=='cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                        @else
                        <span class="badge bg-warning">Pending</span>
                        @endif
                     </td>
                  </tr>
                  <tr>
                     <th>Lead Source</th>
                     <td>{{ $booking->lead_source ?? '-' }}</td>
                  </tr>
                  <tr>
                     <th>Client Name</th>
                     <td>{{ $booking->client_name }}</td>
                  </tr>
                  <tr>
                     <th>Client Contact</th>
                     <td>{{ $booking->client_contact }}</td>
                  </tr>
               </table>
            </div>
            <!-- Right Column -->
            <div class="col-md-6">
               <table class="table table-bordered table-sm">
                  <tr>
                     <th width="40%">Project</th>
                     <td>{{ optional($booking->project)->name }}</td>
                  </tr>
                  <tr>
                     <th>Developer</th>
                     <td>{{ optional($booking->developer)->name }}</td>
                  </tr>
                  <tr>
                     <th>Sales Manager</th>
                     <td>{{ optional($booking->user)->name }}</td>
                  </tr>
                  <tr>
                     <th>Team Leader</th>
                     <td>{{ optional(optional($booking->user)->reportingManager)->name ?? '-' }}</td>
                  </tr>
                  <tr>
                     <th>Sr. Team Leader</th>
                     <td>{{ optional(optional(optional($booking->user)->reportingManager)->reportingManager)->name ?? '-' }}</td>
                  </tr>
                  <tr>
                     <th>Cluster Head</th>
                     <td>
                        {{
                        optional(
                        optional(
                        optional(optional($booking->user)->reportingManager)
                        ->reportingManager
                        )->reportingManager
                        )->name ?? '-'
                        }}
                     </td>
                  </tr>
               </table>
            </div>
         </div>
      </div>
    </div>
   <div class="card mt-4">
      <div class="card-header bg-label-primary">
         <h5 class="mb-0">
            <i class="bx bx-line-chart"></i>
            Brokerage Summary
         </h5>
      </div>
      <div class="card-body">
         <div class="row">
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">Agreement Value</label>
               <div>
                  ₹ {{ number_format($booking->agreement_value ?? 0,2) }}
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">Base Brokerage %</label>
               <div>
                  {{ number_format($booking->base_brokerage_percent ?? 0,2) }}%
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">Site Increment %</label>
               <div>
                  {{ number_format(($booking->site_ladder_percent ?? 0)-($booking->base_brokerage_percent ?? 0),2) }}%
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">AOP %</label>
               <div>
                  {{ number_format($booking->aop_ladder_percent ?? 0,2) }}%
               </div>
            </div>
         </div>
         <hr>
         <div class="row">
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">Total Brokerage %</label>
               <div class="text-primary fw-bold">
                  {{ number_format($booking->total_brokerage_percent ?? 0,2) }}%
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">Revenue</label>
               <div>
                  ₹ {{ number_format($booking->current_effective_amount ?? 0,2) }}
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">Additional Kicker</label>
               <div>
                  ₹ {{ number_format($booking->additional_kicker ?? 0,2) }}
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">Passback</label>
               <div>
                  ₹ {{ number_format($booking->passback ?? 0,2) }}
               </div>
            </div>
         </div>
         <hr>
         <div class="row">
            <div class="col-md-4">
               <label class="form-label fw-bold text-success">
               Final Revenue
               </label>
               <h4 class="text-success">
                  ₹ {{ number_format($booking->final_revenue ?? 0,2) }}
               </h4>
            </div>
         </div>
      </div>
   </div>
   <div class="card mt-4">
      <div class="card-header bg-label-primary d-flex justify-content-between align-items-center">
         <h5 class="mb-0">
            <i class="bx bx-receipt text-primary"></i>
            Invoice History
         </h5>
         <span class="badge bg-primary">
         {{ $booking->brokeragePayments->count() }} Invoice(s)
         </span>
      </div>
      <div class="card-body">
         <div class="row mb-4">
            <div class="col-md-2">
               <strong>Total Invoice %</strong><br>
               {{ number_format($booking->total_invoice_percent,2) }}%
            </div>
            <div class="col-md-2">
               <strong>Invoice Amount</strong><br>
               ₹ {{ number_format($booking->total_invoice_amount,2) }}
            </div>
            <div class="col-md-2">
               <strong>Total GST</strong><br>
               ₹ {{ number_format($booking->total_gst_amount,2) }}
            </div>
            <div class="col-md-2">
               <strong>Grand Invoice</strong><br>
               ₹ {{ number_format($booking->total_grand_invoice_amount,2) }}
            </div>
            <div class="col-md-2">
               <strong>Actual Receipt</strong><br>
               ₹ {{ number_format($booking->total_actual_receipt_amount,2) }}
            </div>
            <div class="col-md-2">
               <strong>Pending Collection</strong><br>
               <span class="text-danger fw-bold">
               ₹ {{ number_format($booking->pending_collection_amount,2) }}
               </span>
            </div>
         </div>
         <div class="table-responsive">
            <table class="table table-bordered table-hover">
               <thead class="table-light">
                  <tr>
                     <th>Invoice No.</th>
                     <th>Type</th>
                     <th>%</th>
                     <th>Invoice</th>
                     <th>GST</th>
                     <th>Grand</th>
                     <th>Actual Receipt</th>
                     <th>Bank Receipt</th>
                     <th>TDS</th>
                     <th>Pending</th>
                     <th>Status</th>
                     <th>Date</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach($booking->brokeragePayments as $payment)
                  @php
                  $grand = $payment->invoice_amount + $payment->total_gst_amount;
                  $pending = max(
                  0,
                  $grand - (
                  $payment->actual_receipt_amount +
                  $payment->tds_amount
                  )
                  );
                  @endphp
                  <tr>
                     <td>{{ $payment->invoice_number ?: '-' }}</td>
                     <td>{{ ucfirst(str_replace('_',' ',$payment->invoice_type)) }}</td>
                     <td>
                        @if($payment->invoice_percent>0)
                        {{ number_format($payment->invoice_percent,2) }}%
                        @else
                        Additional
                        @endif
                     </td>
                     <td>₹ {{ number_format($payment->invoice_amount,2) }}</td>
                     <td>₹ {{ number_format($payment->total_gst_amount,2) }}</td>
                     <td>₹ {{ number_format($grand,2) }}</td>
                     <td>₹ {{ number_format($payment->actual_receipt_amount,2) }}</td>
                     <td>₹ {{ number_format($payment->bank_received_amount,2) }}</td>
                     <td>₹ {{ number_format($payment->tds_amount,2) }}</td>
                     <td>
                        <span class="{{ $pending>0 ? 'text-danger':'text-success' }} fw-bold">
                        ₹ {{ number_format($pending,2) }}
                        </span>
                     </td>
                     <td>
                        <span class="badge bg-{{ $payment->status=='received' ? 'success' : ($payment->status=='partial' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($payment->status) }}
                        </span>
                     </td>
                     <td>{{ $payment->invoice_date }}</td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
      </div>
   </div>
   <div class="card mt-4">
      <div class="card-header bg-label-info">
         <h5 class="mb-0">
            <i class="bx bx-receipt"></i>
            Invoice Summary
         </h5>
      </div>
      <div class="card-body">
         <div class="row">
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               Total Invoice %
               </label>
               <div class="text-primary fw-bold">
                  {{ number_format($booking->total_invoice_percent ?? 0,2) }}%
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               Invoice Amount
               </label>
               <div>
                  ₹ {{ number_format($booking->total_invoice_amount ?? 0,2) }}
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               GST Amount
               </label>
               <div>
                  ₹ {{ number_format($booking->total_gst_amount ?? 0,2) }}
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               Grand Invoice
               </label>
               <div class="fw-bold text-success">
                  ₹ {{ number_format($booking->total_grand_invoice_amount ?? 0,2) }}
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="card mt-4">
      <div class="card-header d-flex bg-label-primary justify-content-between align-items-center">
         <h5 class="mb-0">
            Credit Notes
         </h5>
         <span class="badge bg-label-danger">
         {{ $payments->whereNotNull('credit_note_number')->count() }}
         Credit Notes
         </span>
      </div>
      <div class="card-body p-0">
         <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
               <thead class="table-light">
                  <tr>
                     <th>Credit Note No</th>
                     <th>Date</th>
                     <th>Invoice No</th>
                     <th>Reason</th>
                     <th>Invoice Amount</th>
                     <th>GST</th>
                     <th>Grand Total</th>
                     <th>Bank Receipt</th>
                     <th>TDS</th>
                     <th>Status</th>
                  </tr>
               </thead>
               <tbody>
                  @php
                  $creditNotes = $payments->filter(function($payment){
                  return !empty($payment->credit_note_number);
                  });
                  @endphp
                  @forelse($creditNotes as $payment)
                  <tr>
                     <td>
                        <strong>
                        {{ $payment->credit_note_number }}
                        </strong>
                     </td>
                     <td>
                        {{ optional($payment->credit_note_date)->format('d M Y') }}
                     </td>
                     <td>
                        {{ $payment->invoice_number ?? '-' }}
                     </td>
                     <td>
                        {{ $payment->credit_note_reason ?? '-' }}
                     </td>
                     <td>
                        ₹ {{ number_format($payment->invoice_amount,2) }}
                     </td>
                     <td>
                        ₹ {{ number_format($payment->total_gst_amount,2) }}
                     </td>
                     <td>
                        ₹ {{ number_format(
                        $payment->invoice_amount +
                        $payment->total_gst_amount,
                        2
                        ) }}
                     </td>
                     <td>
                        ₹ {{ number_format($payment->bank_received_amount,2) }}
                     </td>
                     <td>
                        ₹ {{ number_format($payment->tds_amount,2) }}
                     </td>
                     <td>
                        @if($payment->status=='received')
                        <span class="badge bg-success">
                        Received
                        </span>
                        @elseif($payment->status=='partial')
                        <span class="badge bg-warning">
                        Partial
                        </span>
                        @else
                        <span class="badge bg-danger">
                        Pending
                        </span>
                        @endif
                     </td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="10" class="text-center text-muted py-4">
                        No Credit Notes Found
                     </td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>
      <div class="row mb-3">
      <div class="col-md-3">
         <div class="card border-start border-danger border-4">
            <div class="card-body">
               <small>Total Credit Notes</small>
               <h4>
                  {{ $creditNotes->count() }}
               </h4>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card border-start border-warning border-4">
            <div class="card-body">
               <small>Total Credit Amount</small>
               <h4>
                  ₹ {{ number_format(
                  $creditNotes->sum('invoice_amount'),
                  2
                  ) }}
               </h4>
            </div>
         </div>
      </div>
   </div>
   </div>
   
   <div class="card mt-4">
      <div class="card-header bg-label-success">
         <h5 class="mb-0">
            <i class="bx bx-wallet"></i>
            Collection Summary
         </h5>
      </div>
      <div class="card-body">
         <div class="row">
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               Actual Receipt
               </label>
               <div>
                  ₹ {{ number_format($booking->total_actual_receipt_amount ?? 0,2) }}
               </div>
               <small class="text-muted">
               Amount received from Developer (Including GST)
               </small>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               Bank Receipt
               </label>
               <div>
                  ₹ {{ number_format($booking->total_received_amount ?? 0,2) }}
               </div>
               <small class="text-muted">
               Amount credited to Bank (Excluding GST)
               </small>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               TDS Deducted
               </label>
               <div>
                  ₹ {{ number_format(
                  \App\Models\BookingBrokeragePayment::where('booking_id',$booking->id)->sum('tds_amount'),
                  2
                  ) }}
               </div>
               <small class="text-muted">
               Deducted by Developer
               </small>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               Pending Collection
               </label>
               <div class="fw-bold text-danger">
                  ₹ {{ number_format($booking->pending_collection_amount ?? 0,2) }}
               </div>
               <small class="text-muted">
               Grand Invoice − (Actual Receipt + TDS)
               </small>
            </div>
         </div>
         <hr>
         <div class="row">
            <div class="col-md-3">
               <label class="form-label fw-bold">
               Payment Status
               </label>
               <div>
                  @if($booking->payment_status=='completed')
                  <span class="badge bg-success">Completed</span>
                  @elseif($booking->payment_status=='partial')
                  <span class="badge bg-warning">Partial</span>
                  @else
                  <span class="badge bg-danger">Pending</span>
                  @endif
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="card mt-4">
      <div class="card-header bg-label-warning">
         <h5 class="mb-0">
            <i class="bx bx-money"></i>
            Brokerage Settlement
         </h5>
      </div>
      <div class="card-body">
         @php
         $totalTDS = \App\Models\BookingBrokeragePayment::where(
         'booking_id',
         $booking->id
         )->sum('tds_amount');
         $settledBrokerage =
         ($booking->total_received_amount ?? 0)
         + $totalTDS;
         @endphp
         <div class="row">
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               Final Revenue
               </label>
               <div class="text-primary fw-bold">
                  ₹ {{ number_format($booking->final_revenue ?? 0,2) }}
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               Settled Brokerage
               </label>
               <div class="text-success fw-bold">
                  ₹ {{ number_format($settledBrokerage,2) }}
               </div>
               <small class="text-muted">
               Bank Receipt + TDS
               </small>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               Pending Brokerage %
               </label>
               <div>
                  {{ number_format($booking->pending_brokerage_percent ?? 0,2) }}%
               </div>
            </div>
            <div class="col-md-3 mb-3">
               <label class="form-label fw-bold">
               Pending Brokerage
               </label>
               <div class="text-danger fw-bold">
                  ₹ {{ number_format($booking->pending_brokerage_amount ?? 0,2) }}
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="card mt-4">
      <div class="card-header bg-label-primary">
         <h5>Payment Timeline</h5>
      </div>
      <div class="card-body">
         <div class="timeline">
            <!-- Booking Created -->
            <div class="timeline-item">
               <div class="timeline-marker bg-primary"></div>
               <div class="timeline-content">
                  <h6>Booking Created</h6>
                  <small>{{ $booking->booking_date }}</small>
                  <p>
                     Agreement :
                     ₹{{ number_format($booking->agreement_value,2) }}
                     <br>
                     Brokerage :
                     {{ number_format($booking->total_brokerage_percent,2) }}%
                  </p>
               </div>
            </div>
            @foreach($timeline as $payment)
            <div class="timeline-item">
               <div class="timeline-marker bg-success"></div>
               <div class="timeline-content">
                  <h6>
                     {{ ucfirst(str_replace('_',' ',$payment->invoice_type)) }}
                  </h6>
                  <small>{{ $payment->invoice_date }}</small>
                  <p>
                     Invoice :
                     ₹{{ number_format($payment->invoice_amount,2) }}
                     <br>
                     GST :
                     ₹{{ number_format($payment->total_gst_amount,2) }}
                     <br>
                     Grand :
                     ₹{{ number_format($payment->invoice_amount+$payment->total_gst_amount,2) }}
                     <br>
                     Actual Receipt :
                     ₹{{ number_format($payment->actual_receipt_amount,2) }}
                     <br>
                     Bank Receipt :
                     ₹{{ number_format($payment->bank_received_amount,2) }}
                     <br>
                     TDS :
                     ₹{{ number_format($payment->tds_amount,2) }}
                  </p>
               </div>
            </div>
            @endforeach
         </div>
      </div>
   </div>
 
</div>
@endsection