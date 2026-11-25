<?php

if (!function_exists('validateUserForm')) {
    function validateUserForm($request)
    {
        $errors = [];

        // Required fields (keyed by field, so the form can point at each one).
        $required = [
            'employee_code' => 'Employee code', 'first_name' => 'First name', 'last_name' => 'Last name',
            'entity' => 'Company', 'department_id' => 'Department', 'designation_id' => 'Designation',
            'work_location_id' => 'Work location', 'role_id' => 'System role', 'joining_date' => 'Joining date',
            'probation_period_days' => 'Probation (days)', 'annual_ctc' => 'Annual CTC', 'official_email' => 'Official email',
        ];
        foreach ($required as $field => $label) {
            if (! $request->filled($field)) {
                $errors[$field] = "$label is required.";
            }
        }

        // Employee ID and official email sign people in, so they must be unique.
        if ($request->filled('employee_code')
            && \App\Models\User::whereRaw('UPPER(TRIM(employee_code)) = ?', [strtoupper(trim($request->employee_code))])->exists()) {
            $errors['employee_code'] = "Employee code {$request->employee_code} is already used by another employee.";
        }
        if ($request->filled('official_email')
            && \App\Models\User::where('email', trim($request->official_email))->orWhere('official_email', trim($request->official_email))->exists()) {
            $errors['official_email'] = "Official email {$request->official_email} is already used by another employee.";
        }

        // Leaving date & notice
        // if ($request->filled('leaving_date') && !$request->filled('notice_period_days')) {
        //     $errors[] = "Notice period days are required if leaving date is filled";
        // }

        // Bank Details
        // $bankFields = ['bank_account_name','bank_branch_name','bank_account_type','bank_name','ifsc_code','bank_account_number'];
        // foreach ($bankFields as $field) {
        //     if (!$request->filled($field)) {
        //         $errors[] = "$field is required";
        //     }
        // }

        // Offer & Joining Letters (files)
        $offerFile = $request->file('offer_letter_file');
        $joiningFile = $request->file('joining_letter_file');

        // if ($request->filled('offer_letter_sent') && !$offerFile) {
        //     $errors[] = "Offer letter file is required if sent";
        // }
        // if ($request->filled('joining_letter_sent') && !$joiningFile) {
        //     $errors[] = "Joining letter file is required if sent";
        // }

        // Previous Employment Documents
        // if ($request->hasFile('previous_documents')) {
        //     foreach ($request->file('previous_documents') as $file) {
        //         if (!$file->isValid()) {
        //             $errors[] = "One of the previous documents is invalid";
        //         }
        //     }
        // }

        // Photo
        // if (!$request->file('photo')) {
        //     $errors[] = "Photo is required";
        // }

        return $errors;
    }
}
