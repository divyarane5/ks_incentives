<?php

return [
    'GENDER_OPTIONS' => ['Male' => 'Male','Female' => 'Female'],
    'COMPANY_OPTIONS' => ['Home Bazaar Services Pvt Ltd', 'Policy Adda Insurance Brokers Pvt. Ltd.'],
    'BILL_MODES' => ['advance' => 'Advance', 'partial' => 'Partial Advance', 'against' => 'Against'],
    'INDENT_STATUS' => [
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'half-approved' => 'Partial Approved',
                        'closed' => 'Closed'
                    ],
    'INDENT_ITEM_STATUS' => [
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'approve1' => '1st approval',
                        'approve2' => '2nd approval',
                        'approve3' => '3rd approval',
                        'approve4' => '4th approval',
                        'approve5' => '5th approval',
                    ],
    'INDENT_ITEM_COLUMN_MAPPING' => [
                        'pending' => 'approver1',
                        'approved' => '',
                        'rejected' => '',
                        'approve1' => 'approver2',
                        'approve2' => 'approver3',
                        'approve3' => 'approver4',
                        'approve4' => 'approver5',
                        'approve5' => '',
                ],
    'INDENT_CODE_PREFIX' => 'HB-IND-',
    'TRANSPORT_MODE' => [
                    'car' => 'Car',
                    'bike' => 'Bike',
                    'bus' => 'Bus',
                    'auto' => 'Auto',
                    'train' => 'Train',
                    'other' => 'Other'
                ],
    'REIMBURSEMENT_STATUS' => [
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'settled' => 'Settled'
                ],
    'REIMBURSEMENT_CODE_PREFIX' => 'HB-RMB-',
    'MARITAL_STATUS_OPTIONS' => [
                    'single' => 'Single',
                    'married' => 'Married',
                    'divorced' => 'Divorced'
                ],
    'CANDIDATE_STATUS' => [
        'ready' => 'Ready to process',
        'sent' => 'Application Sent',
        'submitted' => 'Application Submitted',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'closed' => 'Closed'
    ],
    'EMPLOYEE_REACHED_TIME' => '11:00 AM',
]

?>
