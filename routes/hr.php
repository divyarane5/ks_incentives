<?php

/*
| KeyPeople: leave, payslips, letters, celebrations (birthday / anniversary posts).
| /me/...   everyone signed in (own leave, payslips, letters; managers approve their team)
| /hr/...   KeyPeople module (HR); the controllers also check user-edit / user-salary-view
*/

use App\Http\Controllers\Hr\CelebrationController;
use App\Http\Controllers\Hr\HrDashboardController;
use App\Http\Controllers\Hr\LeaveController;
use App\Http\Controllers\Hr\MeController;
use App\Http\Controllers\Hr\LetterController;
use App\Http\Controllers\Hr\PasswordController;
use App\Http\Controllers\Hr\PayslipController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Everyone
    Route::get('me', [MeController::class, 'index'])->name('me.dashboard');
    Route::get('me/password', [PasswordController::class, 'edit'])->name('me.password');
    Route::post('me/password', [PasswordController::class, 'update'])->name('me.password.update');
    Route::get('me/leave', [LeaveController::class, 'my'])->name('leave.my');
    Route::post('me/leave', [LeaveController::class, 'store'])->name('leave.store');
    Route::get('me/leave/count', [LeaveController::class, 'count'])->name('leave.count');
    Route::get('me/team-leave', [LeaveController::class, 'team'])->name('leave.team');
    Route::post('leave-request/{leave}/cancel', [LeaveController::class, 'cancel'])->name('leave.cancel');
    Route::post('leave-request/{leave}/decide', [LeaveController::class, 'decide'])->name('leave.decide');

    Route::get('me/payslips', [PayslipController::class, 'mine'])->name('payslip.mine');
    Route::get('payslip/{user}/{fy}/{month}', [PayslipController::class, 'pdf'])
        ->where(['fy' => '\d{4}-\d{2}', 'month' => '\d{1,2}'])->name('payslip.pdf');

    Route::get('me/letters', [LetterController::class, 'mine'])->name('letters.mine');
    Route::get('letter/{letter}/pdf', [LetterController::class, 'pdf'])->name('letters.pdf');

    // HR (KeyPeople module)
    Route::prefix('hr')->group(function () {
        Route::get('dashboard', [HrDashboardController::class, 'index'])->name('hr.dashboard');
        Route::get('leave', [LeaveController::class, 'hr'])->name('leave.hr');
        Route::post('leave/adjust', [LeaveController::class, 'adjust'])->name('leave.adjust');
        Route::get('leave-setup', [LeaveController::class, 'setup'])->name('leave.setup');
        Route::post('leave-types/{type?}', [LeaveController::class, 'saveType'])->name('leave.type.save');
        Route::post('holidays', [LeaveController::class, 'saveHoliday'])->name('leave.holiday.save');
        Route::delete('holidays/{holiday}', [LeaveController::class, 'deleteHoliday'])->name('leave.holiday.delete');

        Route::get('salary-register', [PayslipController::class, 'register'])->name('payslip.register');

        Route::get('letters', [LetterController::class, 'index'])->name('letters.index');
        Route::get('letters/issue', [LetterController::class, 'create'])->name('letters.create');
        Route::post('letters', [LetterController::class, 'store'])->name('letters.store');
        Route::delete('letters/{letter}', [LetterController::class, 'destroy'])->name('letters.destroy');
        Route::post('letters/{letter}/signed', [LetterController::class, 'uploadSigned'])->name('letters.signed');
        Route::post('letters/{letter}/email', [LetterController::class, 'email'])->name('letters.email');
        Route::get('letter-signatories/{signatory?}', [LetterController::class, 'editSignatory'])->name('letters.signatory.edit');
        Route::post('letter-signatories/{signatory?}', [LetterController::class, 'saveSignatory'])->name('letters.signatory.save');
        Route::delete('letter-signatories/{signatory}', [LetterController::class, 'deleteSignatory'])->name('letters.signatory.delete');
        Route::get('letter-templates/{template?}', [LetterController::class, 'editTemplate'])->name('letters.template.edit');
        Route::post('letter-templates/{template?}', [LetterController::class, 'saveTemplate'])->name('letters.template.save');

        Route::get('celebrations', [CelebrationController::class, 'index'])->name('celebrations.index');
        Route::get('celebrations/make', [CelebrationController::class, 'make'])->name('celebrations.make');
        Route::post('celebrations', [CelebrationController::class, 'store'])->name('celebrations.store');
        Route::delete('celebrations/{post}', [CelebrationController::class, 'destroy'])->name('celebrations.destroy');
    });
});
