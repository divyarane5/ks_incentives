@php
    $inr = fn ($n) => \App\Support\PeopleFormat::inr($n, 2, false);
    $earn = $s['earnings']; $ded = $s['deductions'];
    $rows = max(count($earn), count($ded));
    $ek = array_keys($earn); $dk = array_keys($ded);
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Payslip {{ $s['date']->format('F Y') }}</title>
<style>
    @page { margin: 28px 32px; }
    * { font-family: "DejaVu Sans", sans-serif; }
    body { font-size: 10.5px; color: #1f2a3d; }
    .top { border-bottom: 3px solid #c3b246; padding-bottom: 10px; margin-bottom: 14px; }
    .co { font-size: 17px; font-weight: bold; color: #011e48; }
    .muted { color: #6b778c; }
    .title { float: right; text-align: right; }
    .title b { font-size: 14px; color: #011e48; display: block; }
    table { width: 100%; border-collapse: collapse; }
    .info td { padding: 4px 6px; vertical-align: top; }
    .info td.l { color: #6b778c; width: 22%; }
    .info td.v { font-weight: bold; width: 28%; }
    .box { border: 1px solid #d5dbe6; margin-top: 14px; }
    .pay th { background: #011e48; color: #fff; text-align: left; padding: 7px 8px; font-size: 10px; }
    .pay td { padding: 6px 8px; border-bottom: 1px solid #eef1f6; }
    .pay td.n, .pay th.n { text-align: right; }
    .pay tr.tot td { background: #f4f6fa; font-weight: bold; border-top: 1px solid #d5dbe6; }
    .net { margin-top: 14px; background: #011e48; color: #fff; padding: 12px 14px; }
    .net .a { font-size: 18px; font-weight: bold; color: #fff; }
    .net .w { color: #e9e2b0; margin-top: 3px; }
    .foot { margin-top: 22px; font-size: 9px; color: #6b778c; text-align: center; }
</style>
</head>
<body>
    <div class="top">
        <div class="title"><b>Payslip</b>{{ $s['date']->format('F Y') }}</div>
        <div class="co">{{ $s['company'] }}</div>
        @if($s['unit'] && $s['unit'] !== $s['company'])<div class="muted">{{ $s['unit'] }}</div>@endif
    </div>

    <table class="info">
        <tr>
            <td class="l">Name</td><td class="v">{{ $user->name }}</td>
            <td class="l">Employee code</td><td class="v">{{ $user->employee_code ?: '—' }}</td>
        </tr>
        <tr>
            <td class="l">Designation</td><td class="v">{{ $user->designation?->name ?? '—' }}</td>
            <td class="l">Department</td><td class="v">{{ $user->department?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="l">Date of joining</td><td class="v">{{ $user->joining_date ? \Carbon\Carbon::parse($user->joining_date)->format('d M Y') : '—' }}</td>
            <td class="l">Location</td><td class="v">{{ $user->location?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="l">Bank account</td><td class="v">{{ $user->bank_name ? $user->bank_name.' ' : '' }}{{ $user->bank_account_number ? '••'.substr(preg_replace('/\s+/', '', $user->bank_account_number), -4) : '—' }}</td>
            <td class="l">PAN / UAN</td><td class="v">{{ $user->pan_no ? strtoupper($user->pan_no) : '—' }} / {{ $user->uan_number ?: '—' }}</td>
        </tr>
        <tr>
            <td class="l">Days in month</td><td class="v">{{ $s['days_in_month'] }}</td>
            <td class="l">Unpaid leave days</td><td class="v">{{ rtrim(rtrim(number_format($s['unpaid_days'], 1), '0'), '.') ?: '0' }}</td>
        </tr>
    </table>

    <div class="box">
        <table class="pay">
            <tr><th>Earnings</th><th class="n">Amount (₹)</th><th>Deductions</th><th class="n">Amount (₹)</th></tr>
            @for($i = 0; $i < max($rows, 1); $i++)
                <tr>
                    <td>{{ $ek[$i] ?? '' }}</td><td class="n">{{ isset($ek[$i]) ? $inr($earn[$ek[$i]]) : '' }}</td>
                    <td>{{ $dk[$i] ?? '' }}</td><td class="n">{{ isset($dk[$i]) ? $inr($ded[$dk[$i]]) : '' }}</td>
                </tr>
            @endfor
            <tr class="tot">
                <td>Gross earnings</td><td class="n">{{ $inr($s['gross']) }}</td>
                <td>Total deductions</td><td class="n">{{ $inr($s['total_deductions']) }}</td>
            </tr>
        </table>
    </div>

    <div class="net">
        <div>Net pay credited</div>
        <div class="a">₹ {{ $inr($s['net']) }}</div>
        <div class="w">{{ $s['net_words'] }}</div>
    </div>

    <div class="foot">This is a computer-generated payslip and does not need a signature. Generated on {{ now()->format('d M Y') }}.</div>
</body>
</html>
