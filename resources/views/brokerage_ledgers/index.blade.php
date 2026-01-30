@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-4">
        Channel Partner – Brokerage Ledger Payments
    </h4>

    {{-- SUCCESS / ERROR --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    {{-- FILTERS --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-4">
                    <label class="form-label">Channel Partner</label>
                    <select name="channel_partner_id" class="form-select">
                        <option value="">All Channel Partners</option>
                        @foreach ($channelPartners as $cp)
                            <option value="{{ $cp->id }}"
                                {{ request('channel_partner_id') == $cp->id ? 'selected' : '' }}>
                                {{ $cp->firm_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date"
                           name="from_date"
                           value="{{ request('from_date') }}"
                           class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date"
                           name="to_date"
                           value="{{ request('to_date') }}"
                           class="form-control">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">
                        Filter
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- LEDGER TABLE --}}
    <div class="card">
        <div class="card-header">
            <strong>Pending CP Ledgers</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Booking</th>
                        <th>Project</th>
                        <th>Party</th>
                        <th>Channel Partner</th>
                        <th>%</th>
                        <th>Amount</th>
                        <th>Effective From</th>
                        <th>Payment Details</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($ledgers as $ledger)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>#{{ $ledger->booking_id }}</td>

                        <td>{{ $ledger->booking->project->project_name ?? '—' }}</td>
                        <td>
                            @if($ledger->party_type === 'CP')
                                <span class="badge bg-info">Channel Partner</span>
                            @else
                                <span class="badge bg-warning">Company</span>
                            @endif
                        </td>

                        <td>{{ $ledger->channelPartner->firm_name ?? '—' }}</td>

                        <td>{{ $ledger->brokerage_percent }}%</td>

                        <td>₹{{ number_format($ledger->brokerage_amount, 2) }}</td>

                        <td>{{ optional($ledger->effective_from)->format('d-m-Y') }}</td>

                        {{-- PAYMENT FORM --}}
                        <td>
                            @if($ledger->status === 'paid')
                                <div class="small">
                                    <strong>Mode:</strong> {{ $ledger->payment_mode }}
                                    <strong>Date:</strong> {{ optional($ledger->payment_date)->format('d-m-Y') }} <br>
                                    <strong>Ref:</strong> {{ $ledger->reference_no ?? '—' }}
                                    <strong>Payment Status:</strong> {{ucwords($ledger->status)}}
                                </div>
                                
                            @else
                             
                                <form method="POST"
                                    action="{{ route('brokerage_ledgers.markPaid', $ledger->id) }}"
                                    class="row g-1">
                                    @csrf

                                    <div class="col-12 mb-1">
                                        <select name="payment_mode" class="form-select form-select-sm" required>
                                            <option value="">Payment Mode</option>
                                            <option value="NEFT">NEFT</option>
                                            <option value="RTGS">RTGS</option>
                                            <option value="UPI">UPI</option>
                                            <option value="CHEQUE">Cheque</option>
                                            <option value="INTERNAL">Internal</option>
                                        </select>
                                    </div>

                                    <div class="col-12 mb-1">
                                        <input type="date"
                                            name="payment_date"
                                            class="form-control form-control-sm"
                                            required>
                                    </div>

                                    <div class="col-12">
                                        <input type="text"
                                            name="reference_no"
                                            class="form-control form-control-sm"
                                            placeholder="Reference No">
                                    </div>
                            @endif
                        </td>

                       <td class="text-center">
                            <form action="{{ route('brokerage_ledgers.markPaid', $ledger->id) }}"
                                method="POST"
                                onsubmit="return confirm('Mark this ledger as PAID?')">

                                @csrf

                                <input type="hidden" name="payment_mode" value="NEFT">
                                <input type="hidden" name="payment_date" value="{{ now()->toDateString() }}">
                                <input type="hidden" name="reference_no" value="AUTO">

                                <button type="submit" class="btn btn-success btn-sm">
                                    Mark Paid
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            No pending CP ledgers found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
