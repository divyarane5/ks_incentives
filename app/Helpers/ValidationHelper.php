<?php

if (!function_exists('validateUserForm')) {
    function validateUserForm($request)
    {
        $errors = [];

        // Required Text Fields
        $requiredFields = [
            'first_name', 'last_name', 'employee_code', 'entity', 
            'role_id', 'joining_date', 'annual_ctc', 'official_email'
        ];

        foreach ($requiredFields as $field) {
            if (!$request->filled($field)) {
                $errors[] = "$field is required";
            }
        }

        // Employee ID and official email sign people in, so they must be unique.
        if ($request->filled('employee_code')
            && \App\Models\User::whereRaw('UPPER(TRIM(employee_code)) = ?', [strtoupper(trim($request->employee_code))])->exists()) {
            $errors[] = "Employee code {$request->employee_code} is already used by another employee";
        }
        if ($request->filled('official_email')
            && \App\Models\User::where('email', trim($request->official_email))->orWhere('official_email', trim($request->official_email))->exists()) {
            $errors[] = "Official email {$request->official_email} is already used by another employee";
        }

        // Probation & Confirmation Dates
        if (!$request->filled('probation_period_days')) {
            $errors[] = "Probation period is required";
        } elseif (!$request->filled('confirm_date')) {
            $errors[] = "Confirmation date is required";
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
