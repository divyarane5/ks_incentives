@extends('layouts.app')

@section('content')
<div class="container-xxl container-p-y">

    <h4 class="fw-bold mb-3">
        Salary – {{ $user->name }} (FY {{ $fy }})
    </h4>

    {{-- EMPLOYEE INFO --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Employee:</strong><br>
                    {{ $user->name }}
                </div>

                <div class="col-md-4">
                    <strong>Employee Code:</strong><br>
                    {{ $user->employee_code ?? '-' }}
                </div>

                <div class="col-md-4">
                    <strong>Joining Date:</strong><br>
                    {{ $user->joining_date
                        ? \Carbon\Carbon::parse($user->joining_date)->format('d M Y')
                        : '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- SALARY FORM --}}
    <form method="POST" action="{{ route('users.salary.store', $user->id) }}">
        @csrf
        <input type="hidden" name="financial_year" value="{{ $fy }}">

        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th width="180">Salary</th>
                            <th>Remarks</th>
                            <th width="120">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($months as $m)
                        <tr>
                            <td>{{ $m['label'] }}</td>

                            <td>
                                <input type="number"
                                       class="form-control salary-input"
                                       name="salary[{{ $m['year'] }}][{{ $m['month'] }}][amount]"
                                       value="{{ $m['amount'] }}"
                                       {{ !$m['enabled'] ? 'disabled' : '' }}>
                            </td>

                            <td>
                                <input type="text"
                                       class="form-control"
                                       name="salary[{{ $m['year'] }}][{{ $m['month'] }}][remarks]"
                                       value="{{ $m['remarks'] }}"
                                       {{ !$m['enabled'] ? 'disabled' : '' }}>
                            </td>

                            <td>
                                @if(!$m['enabled'])
                                    <span class="badge bg-secondary">Not Joined</span>
                                @elseif($m['status'] === 'Credited')
                                    <span class="badge bg-success">Credited</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="fw-bold">
                            <th>Total</th>
                            <th colspan="3">
                                ₹ <span id="salary-total">{{ number_format($total, 2) }}</span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="mt-3 text-end">
            <button type="submit" class="btn btn-primary">
                Save Salary
            </button>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
document.addEventListener('input', function (e) {
    if (!e.target.classList.contains('salary-input')) return;

    let total = 0;

    document.querySelectorAll('.salary-input').forEach(function (input) {
        if (!input.disabled && input.value !== '') {
            total += parseFloat(input.value);
        }
    });

    document.getElementById('salary-total').innerText =
        total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
});
</script>
@endsection
