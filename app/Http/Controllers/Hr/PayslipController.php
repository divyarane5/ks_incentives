<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSalaryHistory;
use App\Models\User;
use App\Models\UserSalary;
use App\Services\LeaveService;
use App\Support\IndianWords;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

/** Payslips (from the monthly salary page) and the month-wise salary register. */
class PayslipController extends Controller
{
    /** My payslips: one per month that has a saved salary row. */
    public function mine(Request $request)
    {
        $me = $request->user();
        $rows = UserSalary::where('user_id', $me->id)->where('salary_credited', '>', 0)->get()
            ->map(fn ($r) => $this->decorate($r))
            ->sortByDesc('date')->values();

        return view('hr.payslip.mine', ['rows' => $rows, 'user' => $me]);
    }

    /** One payslip as a PDF: yourself, or anyone if you can see salaries. */
    public function pdf(Request $request, User $user, string $fy, int $month)
    {
        $me = $request->user();
        abort_unless((int) $me->id === (int) $user->id || $me->can('user-salary-view'), 403);
        abort_unless(preg_match('/^\d{4}-\d{2}$/', $fy) && $month >= 1 && $month <= 12, 404);

        $row = UserSalary::where('user_id', $user->id)->where('financial_year', $fy)->where('month', $month)->firstOrFail();
        $slip = $this->build($user->loadMissing(['designation', 'department', 'location', 'businessUnit']), $this->decorate($row));

        $pdf = Pdf::loadView('hr.payslip.pdf', ['s' => $slip, 'user' => $user])->setPaper('a4');
        $file = 'payslip-'.($user->employee_code ?: $user->id).'-'.$slip['date']->format('Y-m').'.pdf';

        return $request->boolean('download') ? $pdf->download($file) : $pdf->stream($file);
    }

    /** Salary register for one month: everyone paid, bank details and totals. Excel (CSV) download. */
    public function register(Request $request)
    {
        abort_unless($request->user()->can('user-salary-view'), 403);
        $fy = (string) $request->get('fy', LeaveService::fy(now()->subMonth()));
        if (! preg_match('/^\d{4}-\d{2}$/', $fy)) {
            $fy = LeaveService::fy(now()->subMonth());
        }
        $month = (int) $request->get('month', now()->subMonth()->month);
        $month = max(1, min(12, $month));

        $rows = UserSalary::with(['user.designation', 'user.department'])
            ->where('financial_year', $fy)->where('month', $month)
            ->where(fn ($q) => $q->where('salary_credited', '>', 0)->orWhere('gross_salary', '>', 0))
            ->get()
            ->sortBy(fn ($r) => $r->user?->name)
            ->values();

        $start = (int) substr($fy, 0, 4);
        $date = Carbon::create($month >= 4 ? $start : $start + 1, $month, 1);
        $totals = [
            'gross' => $rows->sum('gross_salary'), 'lop' => $rows->sum('extra_deduction'), 'pf' => $rows->sum('pf_amount'),
            'pt' => $rows->sum('professional_tax'), 'tds' => $rows->sum('tds'), 'net' => $rows->sum('salary_credited'),
            'cost' => $rows->sum('total_employee_cost'),
        ];

        if ($request->get('export') === 'csv') {
            $name = 'salary-register-'.$date->format('Y-m').'.csv';

            return response()->streamDownload(function () use ($rows, $totals) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF"); // so Excel reads ₹ and names correctly
                fputcsv($out, ['Code', 'Name', 'Designation', 'Department', 'Bank', 'Account name', 'Account number', 'IFSC', 'Gross', 'Loss of pay', 'PF', 'PT', 'TDS', 'Net credited', 'Total cost']);
                foreach ($rows as $r) {
                    $u = $r->user;
                    fputcsv($out, [
                        $u?->employee_code, $u?->name, $u?->designation?->name, $u?->department?->name,
                        $u?->bank_name, $u?->bank_account_name, $u?->bank_account_number ? "\t".$u->bank_account_number : '', $u?->ifsc_code,
                        $r->gross_salary, $r->extra_deduction, $r->pf_amount, $r->professional_tax, $r->tds, $r->salary_credited, $r->total_employee_cost,
                    ]);
                }
                fputcsv($out, ['', 'Total', '', '', '', '', '', '', $totals['gross'], $totals['lop'], $totals['pf'], $totals['pt'], $totals['tds'], $totals['net'], $totals['cost']]);
                fclose($out);
            }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $fyOptions = [];
        for ($y = (int) substr(LeaveService::fy(), 0, 4); $y >= 2023; $y--) {
            $fyOptions[] = $y.'-'.substr((string) ($y + 1), 2);
        }
        $missingBank = $rows->filter(fn ($r) => ! $r->user?->bank_account_number || ! $r->user?->ifsc_code)->count();

        return view('hr.payslip.register', compact('rows', 'totals', 'fy', 'month', 'date', 'fyOptions', 'missingBank'));
    }

    /** Adds the calendar date and FY/month to a salary row. */
    private function decorate(UserSalary $r): UserSalary
    {
        $start = (int) substr($r->financial_year, 0, 4);
        $r->date = Carbon::create((int) $r->month >= 4 ? $start : $start + 1, (int) $r->month, 1);

        return $r;
    }

    /** Payslip figures: earnings split like the salary structure that applied that month. */
    private function build(User $user, UserSalary $row): array
    {
        $monthEnd = $row->date->copy()->endOfMonth();
        $hist = EmployeeSalaryHistory::where('user_id', $user->id)->where('effective_from', '<=', $monthEnd->toDateString())
            ->orderByDesc('effective_from')->orderByDesc('id')->first();
        $src = $hist ?: $user;
        $parts = [
            'Basic' => (float) $src->monthly_basic,
            'House rent allowance' => (float) $src->monthly_hra,
            'Special allowance' => (float) $src->special_allowance,
            'Conveyance allowance' => (float) $src->conveyance_allowance,
            'Medical allowance' => (float) $src->medical_reimbursement,
        ];
        $gross = (float) $row->gross_salary;
        $sum = array_sum($parts);
        $earnings = [];
        if ($sum > 0 && $gross > 0) {
            $running = 0;
            $keys = array_keys(array_filter($parts));
            foreach ($keys as $i => $k) {
                $v = $i === count($keys) - 1 ? $gross - $running : round($gross * $parts[$k] / $sum);
                $earnings[$k] = $v;
                $running += $v;
            }
        } else {
            $earnings['Gross salary'] = $gross;
        }

        $deductions = array_filter([
            'Loss of pay' => (float) $row->extra_deduction,
            'Provident fund (employee + employer)' => (float) $row->pf_amount,
            'Professional tax' => (float) $row->professional_tax,
            'Income tax (TDS)' => (float) $row->tds,
        ], fn ($v) => $v > 0);

        $unpaid = LeaveService::unpaidDaysByMonth($user, $row->financial_year)[$row->date->year.'-'.$row->date->month] ?? 0;

        return [
            'date' => $row->date,
            'company' => $user->entity ?: ($user->businessUnit?->name ?? config('app.name')),
            'unit' => $user->businessUnit?->name,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'gross' => $gross,
            'total_deductions' => array_sum($deductions),
            'net' => (float) $row->salary_credited,
            'net_words' => IndianWords::rupees($row->salary_credited),
            'days_in_month' => $row->date->daysInMonth,
            'unpaid_days' => $unpaid,
        ];
    }
}
