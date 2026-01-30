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

Auth::routes();

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

    Route::get('/account', [App\Http\Controllers\DashboardController::class, 'account'])
        ->name('account');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::resource('users', App\Http\Controllers\UserController::class);

    Route::get('/import_users', [App\Http\Controllers\UserController::class, 'importUser'])
        ->name('user.import');

    Route::get('users/{user}/salary', [App\Http\Controllers\UserSalaryController::class, 'index'])
    ->name('users.salary.index');

    Route::post('users/{user}/salary', [App\Http\Controllers\UserSalaryController::class, 'store'])
        ->name('users.salary.store');
    /*
    |--------------------------------------------------------------------------
    | Masters
    |--------------------------------------------------------------------------
    */

    Route::resource('location', App\Http\Controllers\LocationController::class);
    Route::resource('project', App\Http\Controllers\ProjectController::class);
    Route::resource('developer', App\Http\Controllers\DeveloperController::class);
    Route::resource('developer_ladder', App\Http\Controllers\DeveloperLadderController::class);
    Route::resource('project_ladder', App\Http\Controllers\ProjectLadderController::class);
    Route::resource('department', App\Http\Controllers\DepartmentController::class);
    Route::resource('designation', App\Http\Controllers\DesignationController::class);
    Route::resource('business_unit', App\Http\Controllers\BusinessUnitController::class);
    Route::resource('mandate_projects', App\Http\Controllers\MandateProjectController::class);

    /*
    |--------------------------------------------------------------------------
    | Channel Partners
    |--------------------------------------------------------------------------
    */

    Route::resource('channel_partners', App\Http\Controllers\ChannelPartnerController::class);

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

    /*
    |--------------------------------------------------------------------------
    | Invoice
    |--------------------------------------------------------------------------
    */

    Route::resource('invoice', App\Http\Controllers\InvoiceController::class);

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

    Route::resource('expense', App\Http\Controllers\ExpenseController::class);

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

    Route::resource('payment_method', App\Http\Controllers\PaymentMethodController::class);

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

    Route::resource('client_response', App\Http\Controllers\ReferralClientController::class);

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
        ->except(['create', 'store']);

    Route::get('joining_form/download_pdf/{id}', [
        App\Http\Controllers\JoiningFormController::class,
        'downloadPdf'
    ])->name('joining_form.download_pdf');

    /*
    |--------------------------------------------------------------------------
    | Candidate
    |--------------------------------------------------------------------------
    */

    Route::resource('candidate', App\Http\Controllers\CandidateController::class);

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
