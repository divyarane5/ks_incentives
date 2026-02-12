<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserSalaryController extends Controller
{
    // 1️⃣ SHOW SALARY PAGE
    public function index(User $user, Request $request)
    {
        $fy = $request->get('fy', $this->currentFY()); // Example: 2025-26
        [$startYear, $shortEndYear] = explode('-', $fy);

        $startYear = (int) $startYear;

        $fyStart = Carbon::create($startYear, 4, 1);
        $fyEnd   = Carbon::create($startYear + 1, 3, 31);

        $joining = Carbon::parse($user->joining_date)->startOfMonth();
        $salaryStart = $joining->greaterThan($fyStart) ? $joining : $fyStart;

        // ✅ Fetch salary records for this FY
        $existing = UserSalary::where('user_id', $user->id)
            ->where('financial_year', $fy)
            ->get()
            ->keyBy('month'); // key by month only (1–12)

        $months = [];
        $cursor = $fyStart->copy();

        while ($cursor <= $fyEnd) {

            $monthNumber = $cursor->month;

            $record = $existing[$monthNumber] ?? null;

            $months[] = [
                'label'            => $cursor->format('M Y'),
                'month'            => $monthNumber,
                'year'             => $cursor->year,
                'enabled'          => $cursor >= $salaryStart,
                'salary_credited'  => $record->salary_credited ?? 0,
                'remarks'          => $record->remarks ?? null,
                'status'           => $record->status ?? 'Pending',
                'extra_deduction'  => $record->extra_deduction ?? 0,
            ];

            $cursor->addMonth();
        }

        $total = $existing->sum('salary_credited');

        return view('users.salary.index', compact(
            'user', 'fy', 'months', 'total'
        ));
    }




    private function currentFY()
    {
        $today = now();

        if ($today->month >= 4) {
            return $today->year . '-' . substr($today->year + 1, 2);
        }

        return ($today->year - 1) . '-' . substr($today->year, 2);
    }

    public function store(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        foreach ($request->salary as $year => $months) {

            foreach ($months as $month => $data) {

                $credited = $data['salary_credited'] ?? 0;

                if ($credited <= 0) {
                    continue;
                }

                $monthDate = \Carbon\Carbon::create($year, $month, 1);

                $confirmationDate = $user->confirm_date
                    ? \Carbon\Carbon::parse($user->confirm_date)
                    : null;

                // PF Logic
                if (
                    $confirmationDate &&
                    $monthDate->gte($confirmationDate) &&
                    $user->employment_status === 'confirmed'
                ) {
                    $pf = ($user->pf_employee ?? 0) + ($user->pf_employer ?? 0);
                } else {
                    $pf = 0;
                }

                // PT Logic
                if ($user->gender === 'female' && $user->current_ctc < 25000) {
                    $pt = 0;
                } else {
                    $pt = $user->professional_tax ?? 0;
                }

                $standardNet = $user->net_salary ?? 0;

                $deduction = max($standardNet - $credited, 0);

                $gross = $credited + $pt + $pf;

                UserSalary::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'financial_year' => $request->financial_year,
                        'month' => $month
                    ],
                    [
                        'gross_salary' => $gross,
                        'professional_tax' => $pt,
                        'pf_amount' => $pf,
                        'extra_deduction' => $deduction,
                        'system_net_salary' => $standardNet,
                        'salary_credited' => $credited,
                        'total_employee_cost' => $gross,
                        'status' => 'Credited',
                        'remarks' => $data['remarks'] ?? null,
                    ]
                );
            }
        }

        return back()->with('success', 'Salary saved successfully.');
    }






}
