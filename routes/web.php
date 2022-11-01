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
Route::get('reference/{id}', [App\Http\Controllers\ClientController::class, 'reference'])->name('client.reference');
Route::post('rthankyou', [App\Http\Controllers\ClientController::class, 'rthankyou'])->name('client.rthankyou');
Route::get('service/{id}/{sname}', [App\Http\Controllers\ClientController::class, 'service'])->name('client.service');
Route::post('sthankyou', [App\Http\Controllers\ClientController::class, 'sthankyou'])->name('client.sthankyou');
  
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

    //Vendor
    Route::resource('vendor', App\Http\Controllers\VendorController::class)->except(['show', 'index', 'store']);
    Route::get('vendor/index', [App\Http\Controllers\VendorController::class, 'index'])->name('vendor.index');
    Route::post('vendor/store', [App\Http\Controllers\VendorController::class, 'store'])->name('vendor.store');

    //Business Unit
    Route::resource('business_unit', App\Http\Controllers\BusinessUnitController::class);

    //Payment Method
    Route::resource('payment_method', App\Http\Controllers\PaymentMethodController::class);

    //Template
    Route::resource('template', App\Http\Controllers\TemplateController::class);

    //Client
    Route::resource('client', App\Http\Controllers\ClientController::class);

    //Client Response
    Route::resource('client_response', App\Http\Controllers\ReferralClientController::class);
    Route::get('client_response_service', [App\Http\Controllers\ReferralClientController::class, 'sresponse']);

    //profile
    Route::get('/account', [App\Http\Controllers\UserController::class, 'account'])->name('account');
    Route::post('/update_profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('update_profile');

    //roles and permissions
    Route::resource('role', App\Http\Controllers\RoleController::class)->except(['show']);

    //Indent
    Route::resource('indent_configuration', App\Http\Controllers\IndentConfigurationController::class)->except(['show']);

});


