@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-4">
        Brokerage Ledgers – Booking #{{ $booking->id }}
    </h4>

    {{-- BOOKING SUMMARY --}}
    <div class="card mb-4">
        <div class="card-body">
            <strong>Project:</strong> {{ $booking->project->project_name ?? '-' }} <br>
            <strong>Channel Partner:</strong> {{ $booking->channel_partner->firm_name ?? '-' }} <br>
            <strong>Total Project Brokerage %:</strong> {{ $brokerage->brokerage_percent }}% <br>
            <strong>Total Brokerage Amount:</strong>
            ₹{{ number_format($brokerage->brokerage_amount, 2) }}
        </div>
    </div>

    {{-- ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- LEDGER TABLE --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Ledger Entries</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Party</th>
                        <th>Channel Partner</th>
                        <th>%</th>
                        <th>Amount</th>
                        <th>Entry Type</th>
                        <th>Calculation</th>
                        <th>Status</th>
                        <th>Effective From</th>
                        <th>Locked</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ledgers as $ledger)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge {{ $ledger->party_type === 'CP' ? 'bg-info' : 'bg-warning' }}">
                                    {{ $ledger->party_type }}
                                </span>
                            </td>
                            <td>{{ $ledger->channelPartner->firm_name ?? '—' }}</td>
                            <td>{{ $ledger->brokerage_percent }}%</td>
                            <td>₹{{ number_format($ledger->brokerage_amount, 2) }}</td>
                            <td>{{ ucfirst($ledger->entry_type) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $ledger->calculation_type)) }}</td>
                            <td>{{ ucfirst($ledger->status) }}</td>
                            <td>{{ optional($ledger->effective_from)->format('d-m-Y') }}</td>
                            <td>
                                @if ($ledger->is_locked)
                                    <span class="badge bg-secondary">Locked</span>
                                @else
                                    <span class="badge bg-success">Open</span>
                                @endif
                            </td>
                            <td>
                                @if (!$ledger->is_locked)
                                    <form method="POST"
                                          action="{{ route('mandate_bookings.ledgers.destroy', $ledger->id) }}"
                                          onsubmit="return confirm('Delete this ledger entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                No ledger entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 border-end">
                        <h6 class="text-info">Channel Partner (CP) Share</h6>
                        <strong>{{ number_format($cpPercent, 2) }}%</strong><br>
                        ₹{{ number_format($cpAmount, 2) }}
                    </div>
                    <div class="col-md-4 border-end">
                        <h6 class="text-warning">OUR</h6>
                        <strong>{{ number_format($ourPercent, 2) }}% Share</strong><br>
                        ₹{{ number_format($ourAmount, 2) }}
                    </div>
                     <div class="col-md-4">
                        <h6 class="text-warning">TOTAL</h6>
                        <strong>{{ $brokerage->brokerage_percent }}% Share</strong><br>
                        ₹{{ number_format($brokerage->brokerage_amount, 2) }}
                    </div>
                </div>
            </div>
        </div>


    </div>

    {{-- ADD CP ADJUSTMENT --}}
    <div class="card">
        <div class="card-header">
            <strong>Add CP Adjustment Ledger</strong>
            <small class="text-muted d-block">
                Use only when CP ladder changes.
                OUR brokerage will be auto-adjusted by system.
            </small>
        </div>

        <div class="card-body">
            <form method="POST"
                  action="{{ route('mandate_bookings.ledgers.store', $booking->id) }}">
                @csrf

                {{-- FIXED VALUES --}}
                <input type="hidden" name="party_type" value="CP">
                <input type="hidden" name="channel_partner_id" value="{{ $booking->channel_partner_id }}">

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Party</label>
                        <div class="form-control-plaintext fw-bold">
                            Channel Partner
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            Brokerage % Adjustment (CP)
                        </label>
                        <input type="number"
                               step="0.01"
                               name="brokerage_percent"
                               class="form-control"
                               placeholder="+0.50 or -0.25"
                               required>
                        <small class="text-muted">
                            Enter only the change, not total %
                        </small>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Effective From</label>
                        <input type="date"
                               name="effective_from"
                               class="form-control"
                               required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Channel Partner</label>
                        <div class="form-control-plaintext">
                            {{ $booking->channelPartner->firm_name ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Remark</label>
                        <input type="text"
                               name="remark"
                               class="form-control"
                               placeholder="CP ladder upgrade / downgrade">
                    </div>
                </div>

                <button class="btn btn-primary">
                    Add CP Adjustment Ledger
                </button>
            </form>
        </div>
        
    </div>

</div>
@endsection
