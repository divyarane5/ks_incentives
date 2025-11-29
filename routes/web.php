<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('reference/{id}', [App\Http\Controllers\ClientController::class, 'reference'])->name('client.reference');
Route::get('click/{id}', [App\Http\Controllers\ClientController::class, 'click'])->name('client.click');
Route::post('rthankyou', [App\Http\Controllers\ClientController::class, 'rthankyou'])->name('client.rthankyou');
Route::get('service/{id}/{sname}', [App\Http\Controllers\ClientController::class, 'service'])->name('client.service');
Route::post('sthankyou', [App\Http\Controllers\ClientController::class, 'sthankyou'])->name('client.sthankyou');
// Public Channel Partner form
Route::get('/become-channel-partner', [\App\Http\Controllers\ChannelPartnerController::class, 'createPublic'])->name('channel-partner.create.public');
Route::post('/become-channel-partner', [\App\Http\Controllers\ChannelPartnerController::class, 'storePublic'])->name('channel-partner.store.public');
// Show Public Client Enquiry Form
// Route::get('/become-client-enquiry', [App\Http\Controllers\ClientEnquiryController::class, 'createPublic'])->name('client-enquiry.public.create');
// Route::post('/become-client-enquiry', [App\Http\Controllers\ClientEnquiryController::class, 'storePublic'])->name('client-enquiry.public.store');
// Step 1 — Show form
Route::get('/become-client-enquiry', 
    [App\Http\Controllers\ClientEnquiryController::class, 'createPublicStep1']
)->name('client-enquiry.public.create');

// Step 1 — Store
Route::post('/become-client-enquiry-step1', 
    [App\Http\Controllers\ClientEnquiryController::class, 'storePublicStep1']
)->name('client-enquiry.public.storeStep1');

// Step 2 — Show Source of Visit
Route::get('/become-client-enquiry/source', 
    [App\Http\Controllers\ClientEnquiryController::class, 'createPublicSource']
)->name('client-enquiry.public.source');

// Step 2 — Store Source of Visit + Final submit
Route::post('/become-client-enquiry/source', 
    [App\Http\Controllers\ClientEnquiryController::class, 'storePublicSource']
)->name('client-enquiry.public.storeSource');

 // For AJAX search of existing locations
    Route::get('/locations/ajax-search', [App\Http\Controllers\LocationController::class, 'ajaxSearch'])->name('locations.ajaxSearch');


Route::group(['middleware' => 'auth'], function() {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index']);

    //dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/account', [App\Http\Controllers\DashboardController::class, 'account'])->name('account');

    //User
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::get('/import_users', [App\Http\Controllers\UserController::class, 'importUser'])->name('user.import');

    // Route::get('/users/import', [App\Http\Controllers\UserController::class, 'showprocess'])->name('users.import.showprocess');
    // Route::post('/users/import', [App\Http\Controllers\UserController::class, 'import'])->name('users.import');
    // Route::get('/users/import/template', [App\Http\Controllers\UserController::class, 'downloadTemplate'])->name('users.import.template');

    //Location
    Route::resource('location', App\Http\Controllers\LocationController::class);

    //project
    Route::resource('project', App\Http\Controllers\ProjectController::class);
    
    //developer
    Route::resource('developer', App\Http\Controllers\DeveloperController::class);
    
    //aop ladder
    Route::resource('developer_ladder', App\Http\Controllers\DeveloperLadderController::class);
    
    //site ladder
    Route::resource('project_ladder', App\Http\Controllers\ProjectLadderController::class);
    
    //Department
    Route::resource('department', App\Http\Controllers\DepartmentController::class);

    //Designation
    Route::resource('designation', App\Http\Controllers\DesignationController::class);

    //Business Unit
    Route::resource('business_unit', App\Http\Controllers\BusinessUnitController::class);

    //Mandate
    Route::resource('mandate_projects', App\Http\Controllers\MandateProjectController::class);

    //channel Partners + dropdown location addition store
    Route::resource('channel_partners', App\Http\Controllers\ChannelPartnerController::class);
   
   
    // For checking/storing new location (optional if you still use store-on-submit)
    Route::post('/locations/ajax-check-or-store', [App\Http\Controllers\LocationController::class, 'ajaxCheckOrStore'])->name('locations.ajaxCheckOrStore');

    //client enquiry
    Route::resource('client-enquiries', App\Http\Controllers\ClientEnquiryController::class);
    Route::get('client-enquiries/{id}/download',  [App\Http\Controllers\ClientEnquiryController::class, 'download'])->name('client-enquiries.download');
        
    //Booking
    Route::resource('booking', App\Http\Controllers\BookingController::class);
    Route::post('booking/update_status', [App\Http\Controllers\BookingController::class, 'updateStatus'])->name('booking.update_status');
    Route::post('booking/update_istatus', [App\Http\Controllers\BookingController::class, 'updateIStatus'])->name('booking.update_istatus');
    Route::post('booking/update_bstatus', [App\Http\Controllers\BookingController::class, 'updateBStatus'])->name('booking.update_bstatus');
    Route::get('send_booking_mail/{id}', [App\Http\Controllers\BookingController::class, 'sendBookingMail']);

    //Business Unit
    Route::resource('invoice', App\Http\Controllers\InvoiceController::class);

    //profile
    Route::get('/account', [App\Http\Controllers\UserController::class, 'account'])->name('account');
    Route::post('/update_profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('update_profile');

    //roles and permissions
    Route::resource('role', App\Http\Controllers\RoleController::class)->except(['show']);

    //Expense
    Route::resource('expense', App\Http\Controllers\ExpenseController::class);
    Route::post('expense/ajax/store', [App\Http\Controllers\ExpenseController::class, 'ajaxStore'])->name('expense.ajax.store');

    //Vendor
    Route::resource('vendor', App\Http\Controllers\VendorController::class)->except(['show', 'index', 'store']);
    Route::get('vendor/index', [App\Http\Controllers\VendorController::class, 'index'])->name('vendor.index');
    Route::post('vendor/store', [App\Http\Controllers\VendorController::class, 'store'])->name('vendor.store');
    Route::get('vendor_dropdown/{expense_id}', [App\Http\Controllers\VendorController::class, 'getVendorDropdown'])->name('vendor.dropdown');
    Route::post('vendor/ajax/store', [App\Http\Controllers\VendorController::class, 'ajaxStore'])->name('vendor.ajax.store');
    Route::post('vendor/update_status', [App\Http\Controllers\VendorController::class, 'updateStatus'])->name('vendor.update_status');

    //Payment Method
    Route::resource('payment_method', App\Http\Controllers\PaymentMethodController::class);

    //Template
    Route::resource('template', App\Http\Controllers\TemplateController::class);

    //Client
    Route::resource('client', App\Http\Controllers\ClientController::class);
    Route::get('send_referral_mail/{id}', [App\Http\Controllers\ClientController::class, 'sendReferralMail']);

    //Client Response
    Route::resource('client_response', App\Http\Controllers\ReferralClientController::class);
    Route::get('client_response_service', [App\Http\Controllers\ReferralClientController::class, 'sresponse']);

    //Indent configuration
    Route::resource('indent_configuration', App\Http\Controllers\IndentConfigurationController::class)->except(['show']);

    //Indent
    Route::resource('indent', App\Http\Controllers\IndentController::class);
    Route::get('indent-approval', [App\Http\Controllers\IndentController::class, 'indentApproval'])->name('indent.approval');
    Route::post('add-indent-comment', [App\Http\Controllers\IndentController::class, 'indentComment'])->name('indent.comment');
    Route::post('update_indent_item_status', [App\Http\Controllers\IndentController::class, 'UpdateIndentItemStatus'])->name('update_indent_item_status');
    Route::get('indent-closure', [App\Http\Controllers\IndentController::class, 'indentClosure'])->name('indent.closure');
    Route::get('close-indent/{id}', [App\Http\Controllers\IndentController::class, 'closeIndent'])->name('indent.close');
    Route::get('update-payment/{id}', [App\Http\Controllers\IndentController::class, 'updatePayment'])->name('indent.payment_update');
    Route::post('bulk_indent_item_approve', [App\Http\Controllers\IndentController::class, 'bulkIndentItemApprove'])->name('bulk_indent_item_approve');

    Route::resource('reimbursement', App\Http\Controllers\ReimbursementController::class);
    Route::post('update_reimbursement_status', [App\Http\Controllers\ReimbursementController::class, 'updateReimbursementStatus'])->name('update_reimbursement_status');
    Route::post('update_bulk_reimbursement_status', [App\Http\Controllers\ReimbursementController::class, 'updateBulkReimbursementStatus'])->name('update_bulk_reimbursement_status');

    //Reports
    Route::get('indent_payments', [App\Http\Controllers\ReportController::class, 'indentPayments'])->name('reports.indent_payments');
    Route::get('reimbursement_payments', [App\Http\Controllers\ReportController::class, 'reimbursementPayments'])->name('reports.reimbursement_payments');

    Route::resource('joining_form', App\Http\Controllers\JoiningFormController::class)->except(['create', 'store']);
    Route::get('joining_form/download_pdf/{id}', [App\Http\Controllers\JoiningFormController::class, 'downloadPdf'])->name('joining_form.download_pdf');

    //Candidate
    Route::resource('candidate', App\Http\Controllers\CandidateController::class);
    Route::get('send_joining_form/{id}', [App\Http\Controllers\CandidateController::class, 'sendJoiningForm'])->name('candidate.send_form');
    Route::get('candidate.change_status/{id}/{status}', [App\Http\Controllers\CandidateController::class, 'changeStatus'])->name('candidate.change_status');
});

//Joining form
Route::get('joining_form/create/{id}', [App\Http\Controllers\JoiningFormController::class, 'create'])->name('joining_form.create');
Route::post('joining_form', [App\Http\Controllers\JoiningFormController::class, 'store'])->name('joining_form.store');
Route::get('thank_you_joining_form', [App\Http\Controllers\JoiningFormController::class, 'thankYou'])->name('joining_form.thank_you');
Route::get('already_responded_joining_form', [App\Http\Controllers\JoiningFormController::class, 'alreadyResponded'])->name('joining_form.already_responded');
