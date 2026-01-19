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
        $fy = $request->get('fy', $this->currentFY());

        [$startYear, $endYear] = explode('-', $fy);

        $fyStart = Carbon::create($startYear, 4, 1);
        $fyEnd   = Carbon::create(2000 + $endYear, 3, 31);

        $joining = Carbon::parse($user->joining_date)->startOfMonth();

        // salary starts from later of joining date or FY start
        $salaryStart = $joining->greaterThan($fyStart) ? $joining : $fyStart;

        // fetch existing salaries
        $existing = UserSalary::where('user_id', $user->id)
            ->where('financial_year', $fy)
            ->get()
            ->keyBy(fn ($s) => $s->year.'-'.$s->month);

        $months = [];
        $cursor = $fyStart->copy();

        while ($cursor <= $fyEnd) {
            $key = $cursor->year.'-'.$cursor->month;

            $months[] = [
                'label'   => $cursor->format('M Y'),
                'month'   => $cursor->month,
                'year'    => $cursor->year,
                'enabled' => $cursor >= $salaryStart,
                'amount'  => $existing[$key]->credited_amount ?? null,
                'remarks' => $existing[$key]->remarks ?? null,
                'status'  => isset($existing[$key]) ? 'Credited' : 'Pending',
            ];

            $cursor->addMonth();
        }

        $total = $existing->sum('credited_amount');

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

    public function store(User $user, Request $request)
    {
        foreach ($request->salary ?? [] as $months) {
            foreach ($months as $month => $data) {

                if (empty($data['amount'])) {
                    continue;
                }

                UserSalary::updateOrCreate(
                    [
                        'user_id'        => $user->id,
                        'financial_year' => $request->financial_year,
                        'month'          => $month,
                    ],
                    [
                        'credited_amount' => $data['amount'],
                        'remarks'         => $data['remarks'] ?? null,
                        'credited_on'     => now(),
                        'created_by'      => auth()->id(),
                    ]
                );
            }
        }

        return back()->with('success', 'Salary saved successfully');
    }


}
