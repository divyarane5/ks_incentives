<?php

use App\Http\Controllers\RoleController;
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

Route::group(['middleware' => 'auth'], function() {
    Route::get('/', function () {
        return view('dashboard');
    });

    //dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/account', [App\Http\Controllers\DashboardController::class, 'account'])->name('account');

    //User
    Route::resource('users', App\Http\Controllers\UserController::class);

    //Location
    Route::resource('location', App\Http\Controllers\LocationController::class);

    //Department
    Route::resource('department', App\Http\Controllers\DepartmentController::class);

    //Designation
    Route::resource('designation', App\Http\Controllers\DesignationController::class);

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

    //Business Unit
    Route::resource('business_unit', App\Http\Controllers\BusinessUnitController::class);

    //Payment Method
    Route::resource('payment_method', App\Http\Controllers\PaymentMethodController::class);

    //Template
    Route::resource('template', App\Http\Controllers\TemplateController::class);

    //Template
    Route::resource('referral-client', App\Http\Controllers\ReferralClientController::class);

    //profile
    Route::get('/account', [App\Http\Controllers\UserController::class, 'account'])->name('account');
    Route::post('/update_profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('update_profile');

    //roles and permissions
    Route::resource('role', App\Http\Controllers\RoleController::class)->except(['show']);

    //Indent configuration
    Route::resource('indent_configuration', App\Http\Controllers\IndentConfigurationController::class)->except(['show']);

    //Indent
    Route::resource('indent', App\Http\Controllers\IndentController::class);
    Route::get('indent-approval', [App\Http\Controllers\IndentController::class, 'indentApproval'])->name('indent.approval');
    Route::post('add-indent-comment', [App\Http\Controllers\IndentController::class, 'indentComment'])->name('indent.comment');
    Route::post('update_indent_item_status', [App\Http\Controllers\IndentController::class, 'UpdateIndentItemStatus'])->name('update_indent_item_status');
    Route::get('indent-closure', [App\Http\Controllers\IndentController::class, 'indentClosure'])->name('indent.closure');
    Route::get('close-indent/{id}', [App\Http\Controllers\IndentController::class, 'closeIndent'])->name('indent.close');
});


