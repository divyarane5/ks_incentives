<?php

return [
    'GENDER_OPTIONS' => ['Male' => 'Male','Female' => 'Female'],
    'COMPANY_OPTIONS' => ['Home Bazaar Services Pvt Ltd', 'Policy Adda Insurance Brokers Pvt. Ltd.'],
    'BILL_MODES' => ['advance' => 'Advance', 'partial' => 'Partial Advance', 'against' => 'Against'],
    'INDENT_STATUS' => [
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'half-approved' => 'Half Approved',
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
                    ]
]

?>
