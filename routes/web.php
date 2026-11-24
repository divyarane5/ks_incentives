<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider within a group
| which contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['reset' => false]);

// Forgot password: a 6-digit code to the official email (replaces the old reset-link emails).
Route::get('password/reset', [App\Http\Controllers\Auth\PasswordOtpController::class, 'request'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\PasswordOtpController::class, 'send'])->name('password.email')->middleware('throttle:20,1');
Route::get('password/code', [App\Http\Controllers\Auth\PasswordOtpController::class, 'form'])->name('password.otp');
Route::post('password/code', [App\Http\Controllers\Auth\PasswordOtpController::class, 'verify'])->name('password.otp.verify')->middleware('throttle:20,1');
Route::post('password/code/resend', [App\Http\Controllers\Auth\PasswordOtpController::class, 'resend'])->name('password.otp.resend')->middleware('throttle:10,1');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('reference/{id}', [App\Http\Controllers\ClientController::class, 'reference'])
    ->name('client.reference');

Route::get('click/{id}', [App\Http\Controllers\ClientController::class, 'click'])
    ->name('client.click');

Route::post('rthankyou', [App\Http\Controllers\ClientController::class, 'rthankyou'])
    ->name('client.rthankyou');

Route::get('service/{id}/{sname}', [App\Http\Controllers\ClientController::class, 'service'])
    ->name('client.service');

Route::post('sthankyou', [App\Http\Controllers\ClientController::class, 'sthankyou'])
    ->name('client.sthankyou');

/*
|--------------------------------------------------------------------------
| Public Channel Partner Form
|--------------------------------------------------------------------------
*/

Route::get('/become-channel-partner', [App\Http\Controllers\ChannelPartnerController::class, 'createPublic'])
    ->name('channel-partner.create.public');

Route::post('/become-channel-partner', [App\Http\Controllers\ChannelPartnerController::class, 'storePublic'])
    ->name('channel-partner.store.public');

/*
|--------------------------------------------------------------------------
| Public Client Enquiry (Multi-step)
|--------------------------------------------------------------------------
*/

// Step 1 — Show form
Route::get('/become-client-enquiry', [
    App\Http\Controllers\ClientEnquiryController::class,
    'createPublicStep1'
])->name('client-enquiry.public.create');

// Step 1 — Store
Route::post('/become-client-enquiry-step1', [
    App\Http\Controllers\ClientEnquiryController::class,
    'storePublicStep1'
])->name('client-enquiry.public.storeStep1');

// Step 2 — Show Source of Visit
Route::get('/become-client-enquiry/source', [
    App\Http\Controllers\ClientEnquiryController::class,
    'createPublicSource'
])->name('client-enquiry.public.source');

// Step 2 — Store Source of Visit + Final submit
Route::post('/become-client-enquiry/source', [
    App\Http\Controllers\ClientEnquiryController::class,
    'storePublicSource'
])->name('client-enquiry.public.storeSource');

/*
|--------------------------------------------------------------------------
| AJAX & Public Utilities
|--------------------------------------------------------------------------
*/

Route::get('/locations/ajax-search', [
    App\Http\Controllers\LocationController::class,
    'ajaxSearch'
])->name('locations.ajaxSearch');

Route::get('/card/{slug}', [
    App\Http\Controllers\UserController::class,
    'card'
])->name('users.card');

Route::get('/user/{id}/vcf', [
    App\Http\Controllers\UserController::class,
    'downloadVcf'
])->name('user.vcf');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index']);

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    // RealtySuite: who can open which module (KeyPeople), and a person's own incentive (KeyEarn)
    Route::get('/module-access', [App\Http\Controllers\ModuleAccessController::class, 'index'])->name('module-access.index');
    Route::post('/module-access/roles', [App\Http\Controllers\ModuleAccessController::class, 'updateRoles'])->name('module-access.roles');
    Route::post('/module-access/user', [App\Http\Controllers\ModuleAccessController::class, 'updateUser'])->name('module-access.user');
    Route::get('/my-incentive', [App\Http\Controllers\MyIncentiveController::class, 'show'])->name('my-incentive');
    Route::match(['get','post'],'/incentive-dashboard-view',[App\Http\Controllers\DashboardController::class,'dashboardTwo']);
    Route::get('/incentive-dashboard-view-reset',[App\Http\Controllers\DashboardController::class,'resetDashboard']);

    Route::get('/account', [App\Http\Controllers\DashboardController::class, 'account'])
        ->name('account');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::resource('users', App\Http\Controllers\UserController::class);
    //Route::get('/org_tree', [App\Http\Controllers\UserController::class, 'orgTree'])->name('user.orgtree');
    Route::get('/org_tree', [App\Http\Controllers\UserController::class, 'orgTree'])->middleware('permission:user-org-tree-view');
    //Route::get('/import_users', [App\Http\Controllers\UserController::class, 'importUser'])->name('user.import');
    Route::get('/import_users', [App\Http\Controllers\UserController::class, 'showImportForm'])->name('user.import.form');
    Route::post('/import_users', [App\Http\Controllers\UserController::class, 'importUser'])->name('user.import');
    
    Route::get('users/{user}/salary', [App\Http\Controllers\UserSalaryController::class, 'index'])
    ->name('users.salary.index');

    Route::post('users/{user}/salary', [App\Http\Controllers\UserSalaryController::class, 'store'])
        ->name('users.salary.store');
    
    Route::post('/update-password', [App\Http\Controllers\Hr\PasswordController::class, 'update'])
    ->name('update_password')
    ->middleware('auth');
    /*
    |--------------------------------------------------------------------------
    | Masters
    |--------------------------------------------------------------------------
    */

    Route::resource('location', App\Http\Controllers\LocationController::class)->except(['show']); // no detail page
    Route::resource('project', App\Http\Controllers\ProjectController::class)->except(['show']); // no detail page
    Route::resource('developer', App\Http\Controllers\DeveloperController::class)->except(['show']); // no detail page
    Route::resource('company-bank',  App\Http\Controllers\CompanyBankAccountController::class)->except(['show']); // no detail page
    Route::resource('department', App\Http\Controllers\DepartmentController::class)->except(['show']); // no detail page
    Route::resource('designation', App\Http\Controllers\DesignationController::class)->except(['show']); // no detail page
    Route::resource('business_unit', App\Http\Controllers\BusinessUnitController::class)->except(['show']); // no detail page
    Route::resource('mandate_projects', App\Http\Controllers\MandateProjectController::class)->except(['show']); // no detail page

    /*
    |--------------------------------------------------------------------------
    | Channel Partners
    |--------------------------------------------------------------------------
    */

    Route::resource('channel_partners', App\Http\Controllers\ChannelPartnerController::class)->except(['show']); // no detail page

    Route::post('channel-partners/quick-store', [
        App\Http\Controllers\ChannelPartnerController::class,
        'quickStore'
    ])->name('channel-partners.quick-store');

    /*
    |--------------------------------------------------------------------------
    | Mandate Bookings
    |--------------------------------------------------------------------------
    */

    Route::resource('mandate_bookings', App\Http\Controllers\MandateBookingController::class);

    Route::post('mandate-bookings/update-status', [
        App\Http\Controllers\MandateBookingController::class,
        'updateStatus'
    ])->name('mandate_bookings.updateStatus');
    Route::get(
        '/mandate-bookings/{booking}/ledgers',
        [App\Http\Controllers\BookingLedgerController::class, 'index']
    )->name('mandate_bookings.ledgers');

    Route::post(
        '/mandate-bookings/{booking}/ledgers',
        [App\Http\Controllers\BookingLedgerController::class, 'store']
    )->name('mandate_bookings.ledgers.store');

    // Delete ledger (only unlocked + adjustment)
    Route::delete('ledgers/{ledger}', [App\Http\Controllers\BookingLedgerController::class, 'destroy'])
        ->name('mandate_bookings.ledgers.destroy');

    Route::get(
        '/brokerage-ledgers',
        [App\Http\Controllers\BookingLedgerController::class, 'cpIndex']
    )->name('brokerage_ledgers.index');

    Route::post(
        '/brokerage-ledgers/{ledger}/mark-paid',
        [App\Http\Controllers\BookingLedgerController::class, 'markPaid']
    )->name('brokerage_ledgers.markPaid');
    /*
    |--------------------------------------------------------------------------
    | Locations (AJAX)
    |--------------------------------------------------------------------------
    */

    Route::post('/locations/ajax-check-or-store', [
        App\Http\Controllers\LocationController::class,
        'ajaxCheckOrStore'
    ])->name('locations.ajaxCheckOrStore');

    /*
    |--------------------------------------------------------------------------
    | Client Enquiries (Permission Protected)
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'permission:client-enquiry-view',
        'check.bu:AI'
    ])->group(function () {

        Route::resource('client-enquiries', App\Http\Controllers\ClientEnquiryController::class);

        Route::get('client-enquiries/{id}/download', [
            App\Http\Controllers\ClientEnquiryController::class,
            'download'
        ])->name('client-enquiries.download');

        Route::get('client-enquiries/{id}/updates', [
            App\Http\Controllers\ClientEnquiryUpdateController::class,
            'create'
        ])->name('client-enquiries.updates');

        Route::post('client-enquiries/{id}/updates', [
            App\Http\Controllers\ClientEnquiryUpdateController::class,
            'store'
        ])->name('client-enquiries.updates.store');

        Route::get('client-enquiries/{id}/history', [
            App\Http\Controllers\ClientEnquiryController::class,
            'history'
        ])->name('client-enquiries.history');
    });

    /*
    |--------------------------------------------------------------------------
    | Bookings
    |--------------------------------------------------------------------------
    */
    Route::get('booking/projects', [App\Http\Controllers\BookingController::class,'projects'])
    ->name('booking.projects');

    Route::get('booking/developers', [App\Http\Controllers\BookingController::class,'developers'])
    ->name('booking.developers');

    Route::get('booking/users-by-role', [
    App\Http\Controllers\BookingController::class,
    'usersByRole'
    ])->name('booking.users-by-role');

    Route::get('booking/users-hierarchy',
        [App\Http\Controllers\BookingController::class,'usersByHierarchy']
    )->name('booking.users-hierarchy');
    Route::get(
        '/booking/export',
        [App\Http\Controllers\BookingController::class, 'export']
    )->name('booking.export');
    Route::resource('booking', App\Http\Controllers\BookingController::class);
    Route::post('booking/update_status', [
        App\Http\Controllers\BookingController::class,
        'updateStatus'
    ])->name('booking.update_status');

    Route::post('booking/update_istatus', [
        App\Http\Controllers\BookingController::class,
        'updateIStatus'
    ])->name('booking.update_istatus');

    Route::post('booking/update_bstatus', [
        App\Http\Controllers\BookingController::class,
        'updateBStatus'
    ])->name('booking.update_bstatus');

    Route::get('send_booking_mail/{id}', [
        App\Http\Controllers\BookingController::class,
        'sendBookingMail'
    ]);
    Route::get(
        'booking/{id}/pdf',
        [App\Http\Controllers\BookingController::class, 'downloadPdf']
    )->name('booking.pdf');
    
    
    /*
    |--------------------------------------------------------------------------
    | Booking Brokerage Payment - custom routes FIRST
    |--------------------------------------------------------------------------
    */

    Route::post('/booking-brokerage-payment/store',
        [App\Http\Controllers\BookingBrokeragePaymentController::class,'store']
    )->name('booking.brokerage.payment.store');

    Route::get('booking/payment-history/{id}',
        [App\Http\Controllers\BookingBrokeragePaymentController::class,'history']
    )->name('booking.payment.history');

    Route::match(['post','put'], 'booking/payment-update/{id}',
        [App\Http\Controllers\BookingBrokeragePaymentController::class,'update']
    )->name('booking.payment.update');

    Route::get(
        'developer-billing/{developer}',
        [App\Http\Controllers\BookingBrokeragePaymentController::class,
        'getDeveloperBillingEntities']
    );

    Route::get(
        'company-bank-accounts',
        [App\Http\Controllers\BookingBrokeragePaymentController::class,
        'getCompanyBankAccounts']
    );

    /*
    |--------------------------------------------------------------------------
    | AJAX dropdown routes (IMPORTANT: must be before resource)
    |--------------------------------------------------------------------------
    */

    Route::get(
        'brokerage-payments/projects',
        [App\Http\Controllers\BookingBrokeragePaymentController::class,'projects']
    )->name('brokerage-payments.projects');

    Route::get(
        'brokerage-payments/developers',
        [App\Http\Controllers\BookingBrokeragePaymentController::class,'developers']
    )->name('brokerage-payments.developers');

    /*
    |--------------------------------------------------------------------------
    | Resource route (MUST be after everything)
    |--------------------------------------------------------------------------
    */
    Route::get(
        'brokerage-payments/export',
        [App\Http\Controllers\BookingBrokeragePaymentController::class,'export']
    )->name('brokerage-payments.export');

    Route::resource(
        'brokerage-payments',
        App\Http\Controllers\BookingBrokeragePaymentController::class
    )->except(['show']);
    Route::get(
        'brokerage-payments-aging',
        [App\Http\Controllers\BookingBrokeragePaymentController::class,'agingReport']
    )->name('brokerage-payments.aging');
     
    /*
    |--------------------------------------------------------------------------
    | Datatable + Summary
    |--------------------------------------------------------------------------
    */

    Route::get(
        'brokerage-payments-datatable',
        [App\Http\Controllers\BookingBrokeragePaymentController::class, 'datatable']
    )->name('brokerage-payments.datatable');

    Route::get(
        'brokerage-payments-summary',
        [App\Http\Controllers\BookingBrokeragePaymentController::class,'summary']
    )->name('brokerage-payments.summary');
    
    /*
    |--------------------------------------------------------------------------
    | Invoice
    |--------------------------------------------------------------------------
    */

    Route::resource('invoice', App\Http\Controllers\InvoiceController::class)->except(['show']); // no detail page


    Route::resource('incentive-slabs',App\Http\Controllers\IncentiveSlabController::class)->except(['show']); // no detail page

    // Quarterly incentives on the year so far, with freeze
    Route::get('incentive-ledger', [App\Http\Controllers\IncentiveLedgerController::class, 'index'])->name('incentive-ledger.index');
    Route::post('incentive-ledger/freeze', [App\Http\Controllers\IncentiveLedgerController::class, 'freeze'])->name('incentive-ledger.freeze');
    Route::post('incentive-ledger/unfreeze', [App\Http\Controllers\IncentiveLedgerController::class, 'unfreeze'])->name('incentive-ledger.unfreeze');
    Route::get('incentive-ledger/export', [App\Http\Controllers\IncentiveLedgerController::class, 'export'])->name('incentive-ledger.export');

    Route::prefix('incentives')->group(function () {

        Route::get('/preview', [App\Http\Controllers\IncentiveController::class, 'preview'])
            ->name('incentives.preview');

        Route::post('/preview-data', [App\Http\Controllers\IncentiveController::class, 'previewData'])
            ->name('incentives.preview.data');

    });
    Route::get('/incentives/{user}',[App\Http\Controllers\IncentiveController::class, 'show'])->name('incentives.show');
    Route::post('/incentives/save',[App\Http\Controllers\IncentiveController::class, 'save'])->name('incentives.save');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/account', [App\Http\Controllers\UserController::class, 'account'])
        ->name('account');

    Route::post('/update_profile', [App\Http\Controllers\UserController::class, 'updateProfile'])
        ->name('update_profile');

    /*
    |--------------------------------------------------------------------------
    | Roles & Permissions
    |--------------------------------------------------------------------------
    */

    Route::resource('role', App\Http\Controllers\RoleController::class)
        ->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | Expenses
    |--------------------------------------------------------------------------
    */

    Route::resource('expense', App\Http\Controllers\ExpenseController::class)->except(['show']); // no detail page

    Route::post('expense/ajax/store', [
        App\Http\Controllers\ExpenseController::class,
        'ajaxStore'
    ])->name('expense.ajax.store');

    /*
    |--------------------------------------------------------------------------
    | Vendors
    |--------------------------------------------------------------------------
    */

    Route::resource('vendor', App\Http\Controllers\VendorController::class)
        ->except(['show', 'index', 'store']);

    Route::get('vendor/index', [App\Http\Controllers\VendorController::class, 'index'])
        ->name('vendor.index');

    Route::post('vendor/store', [App\Http\Controllers\VendorController::class, 'store'])
        ->name('vendor.store');

    Route::get('vendor_dropdown/{expense_id}', [
        App\Http\Controllers\VendorController::class,
        'getVendorDropdown'
    ])->name('vendor.dropdown');

    Route::post('vendor/ajax/store', [
        App\Http\Controllers\VendorController::class,
        'ajaxStore'
    ])->name('vendor.ajax.store');

    Route::post('vendor/update_status', [
        App\Http\Controllers\VendorController::class,
        'updateStatus'
    ])->name('vendor.update_status');

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    */

    Route::resource('payment_method', App\Http\Controllers\PaymentMethodController::class)->except(['show']); // no detail page

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    */

    Route::resource('template', App\Http\Controllers\TemplateController::class);

    /*
    |--------------------------------------------------------------------------
    | Clients
    |--------------------------------------------------------------------------
    */

    Route::resource('client', App\Http\Controllers\ClientController::class);

    Route::get('send_referral_mail/{id}', [
        App\Http\Controllers\ClientController::class,
        'sendReferralMail'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Client Response
    |--------------------------------------------------------------------------
    */

    Route::resource('client_response', App\Http\Controllers\ReferralClientController::class)->only(['index', 'show', 'update']); // responses come from the public form

    Route::get('client_response_service', [
        App\Http\Controllers\ReferralClientController::class,
        'sresponse'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Indents
    |--------------------------------------------------------------------------
    */

    Route::resource('indent_configuration', App\Http\Controllers\IndentConfigurationController::class)
        ->except(['show']);

    Route::resource('indent', App\Http\Controllers\IndentController::class);

    Route::get('indent-approval', [
        App\Http\Controllers\IndentController::class,
        'indentApproval'
    ])->name('indent.approval');

    Route::post('add-indent-comment', [
        App\Http\Controllers\IndentController::class,
        'indentComment'
    ])->name('indent.comment');

    Route::post('update_indent_item_status', [
        App\Http\Controllers\IndentController::class,
        'UpdateIndentItemStatus'
    ])->name('update_indent_item_status');

    Route::get('indent-closure', [
        App\Http\Controllers\IndentController::class,
        'indentClosure'
    ])->name('indent.closure');

    Route::get('close-indent/{id}', [
        App\Http\Controllers\IndentController::class,
        'closeIndent'
    ])->name('indent.close');

    Route::get('update-payment/{id}', [
        App\Http\Controllers\IndentController::class,
        'updatePayment'
    ])->name('indent.payment_update');

    Route::post('bulk_indent_item_approve', [
        App\Http\Controllers\IndentController::class,
        'bulkIndentItemApprove'
    ])->name('bulk_indent_item_approve');

    /*
    |--------------------------------------------------------------------------
    | Reimbursements
    |--------------------------------------------------------------------------
    */

    Route::resource('reimbursement', App\Http\Controllers\ReimbursementController::class);

    Route::post('update_reimbursement_status', [
        App\Http\Controllers\ReimbursementController::class,
        'updateReimbursementStatus'
    ])->name('update_reimbursement_status');

    Route::post('update_bulk_reimbursement_status', [
        App\Http\Controllers\ReimbursementController::class,
        'updateBulkReimbursementStatus'
    ])->name('update_bulk_reimbursement_status');

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get('indent_payments', [
        App\Http\Controllers\ReportController::class,
        'indentPayments'
    ])->name('reports.indent_payments');

    Route::get('reimbursement_payments', [
        App\Http\Controllers\ReportController::class,
        'reimbursementPayments'
    ])->name('reports.reimbursement_payments');

    /*
    |--------------------------------------------------------------------------
    | Joining Form (Internal)
    |--------------------------------------------------------------------------
    */

    Route::resource('joining_form', App\Http\Controllers\JoiningFormController::class)
        ->except(['index', 'create', 'store', 'destroy']);

    Route::get('joining_form/download_pdf/{id}', [
        App\Http\Controllers\JoiningFormController::class,
        'downloadPdf'
    ])->name('joining_form.download_pdf');

    /*
    |--------------------------------------------------------------------------
    | Candidate
    |--------------------------------------------------------------------------
    */

    Route::resource('candidate', App\Http\Controllers\CandidateController::class)->except(['show']); // no detail page

    Route::get('send_joining_form/{id}', [
        App\Http\Controllers\CandidateController::class,
        'sendJoiningForm'
    ])->name('candidate.send_form');

    Route::get('candidate.change_status/{id}/{status}', [
        App\Http\Controllers\CandidateController::class,
        'changeStatus'
    ])->name('candidate.change_status');
});

/*
|--------------------------------------------------------------------------
| Joining Form (Public)
|--------------------------------------------------------------------------
*/

Route::get('joining_form/create/{id}', [
    App\Http\Controllers\JoiningFormController::class,
    'create'
])->name('joining_form.create');

Route::post('joining_form', [
    App\Http\Controllers\JoiningFormController::class,
    'store'
])->name('joining_form.store');

Route::get('thank_you_joining_form', [
    App\Http\Controllers\JoiningFormController::class,
    'thankYou'
])->name('joining_form.thank_you');

Route::get('already_responded_joining_form', [
    App\Http\Controllers\JoiningFormController::class,
    'alreadyResponded'
])->name('joining_form.already_responded');

// KeyPeople: leave, payslips, letters
require __DIR__.'/hr.php';
