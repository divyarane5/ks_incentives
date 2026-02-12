<?php

namespace App\Services;

use App\Models\User;
use App\Models\EmployeeSalaryHistory;
use App\Models\EmployeeExitHistory;
use App\Models\EmployeeReportingManagerHistory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeService
{
    /**
     * =========================
     * SALARY MANAGEMENT
     * =========================
     */
    public function updateSalary(User $user, array $data): void
    {
        DB::transaction(function () use ($user, $data) {

            EmployeeSalaryHistory::create([
                'user_id'              => $user->id,
                'annual_ctc'           => $data['annual_ctc'],
                'monthly_basic'        => $data['monthly_basic'] ?? null,
                'monthly_hra'          => $data['monthly_hra'] ?? null,
                'special_allowance'    => $data['special_allowance'] ?? null,
                'conveyance_allowance' => $data['conveyance_allowance'] ?? null,
                'medical_reimbursement'=> $data['medical_reimbursement'] ?? null,
                'professional_tax'     => $data['professional_tax'] ?? null,
                'pf_employer'          => $data['pf_employer'] ?? null,
                'pf_employee'          => $data['pf_employee'] ?? null,
                'net_deductions'       => $data['net_deductions'] ?? null,
                'net_salary'           => $data['net_salary'],
                'effective_from'       => $data['effective_from'] ?? now(),
                'changed_by'           => auth()->id(),
                'remarks'              => $data['remarks'] ?? null,
            ]);

            // Update CURRENT snapshot on users table
            $user->update([
                'annual_ctc'    => $data['annual_ctc'],
                'current_ctc'   => $data['annual_ctc'], // keep backward compatibility
                'monthly_basic' => $data['monthly_basic'] ?? null,
                'net_salary'    => $data['net_salary'],
            ]);
        });
    }

    /**
     * =========================
     * REPORTING MANAGER CHANGE
     * =========================
     */
    public function changeReportingManager(User $user, int $newManagerId, ?string $remarks = null): void
    {
        DB::transaction(function () use ($user, $newManagerId, $remarks) {

            // Close current manager
            EmployeeReportingManagerHistory::where('user_id', $user->id)
                ->whereNull('effective_to')
                ->update(['effective_to' => now()]);

            // Add new manager
            EmployeeReportingManagerHistory::create([
                'user_id'              => $user->id,
                'reporting_manager_id' => $newManagerId,
                'effective_from'       => now(),
                'changed_by'           => auth()->id(),
                'remarks'              => $remarks,
            ]);

            // Optional snapshot update (for fast access)
            $user->update([
                'reporting_manager_id' => $newManagerId,
            ]);
        });
    }

    /**
     * =========================
     * EXIT EMPLOYEE
     * =========================
     */
    public function exitEmployee(User $user, array $data): void
    {
        DB::transaction(function () use ($user, $data) {

            EmployeeExitHistory::create([
                'user_id'        => $user->id,
                'exit_date'      => $data['exit_date'],
                'exit_type'      => $data['exit_type'], // Resigned / Terminated / Absconded
                'exit_reason'    => $data['exit_reason'] ?? null,
                'remarks'        => $data['remarks'] ?? null,
                'approved_by'    => auth()->id(),
                'is_rehirable'   => $data['is_rehirable'] ?? true,
            ]);

            $user->update([
                'status'        => 'Inactive',
                'exit_status'   => $data['exit_type'],
                'leaving_date'  => $data['exit_date'],
                'notice_period_days' => $data['notice_period_days'] ?? null,
            ]);
        });
    }

    /**
     * =========================
     * REJOIN EMPLOYEE
     * =========================
     */
    public function rejoinEmployee(User $user, array $data): void
    {
        DB::transaction(function () use ($user, $data) {

            $joiningDate = Carbon::parse($data['joining_date']);
            $probationDays = $data['probation_period_days'] ?? 0;

            $user->update([
                'status'                   => 'Active',
                'joining_date'             => $joiningDate,
                'probation_period_days'    => $probationDays,
                'confirm_date'             => $probationDays
                    ? $joiningDate->copy()->addDays($probationDays)
                    : null,
                'employment_status'        => 'Probation',
                'leaving_date'             => null,
                'exit_status'              => null,
            ]);

            // New salary history
            $this->updateSalary($user, $data['salary']);

            // New reporting manager
            $this->changeReportingManager(
                $user,
                $data['reporting_manager_id'],
                'Rejoined employee'
            );
        });
    }

    /**
     * =========================
     * CONFIRM EMPLOYEE
     * =========================
     */
    public function confirmEmployee(User $user): void
    {
        $user->update([
            'employment_status' => 'Confirmed',
            'confirm_date'      => now(),
            'pf_status'         => true,
            'pf_joining_date'   => now(),
        ]);
    }
}
