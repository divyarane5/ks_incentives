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

    //Expense
    Route::resource('vendors', App\Http\Controllers\VendorController::class);

    //profile
    Route::get('/account', [App\Http\Controllers\UserController::class, 'account'])->name('account');
    Route::post('/update_profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('update_profile');

    //roles and permissions
    Route::resource('role', App\Http\Controllers\RoleController::class)->except(['show']);

});


