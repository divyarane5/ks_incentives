<?php

/*
|--------------------------------------------------------------------------
| KeyLeads (leads / sales CRM)
|--------------------------------------------------------------------------
| Loaded with the "web" middleware. Every name starts with "crm." and the
| screens live under /crm. Sign-in is the shared RealtySuite login.
*/

use App\Http\Controllers\Crm\AlertController;
use App\Http\Controllers\Crm\AssignmentRuleController;
use App\Http\Controllers\Crm\CaptureController;
use App\Http\Controllers\Crm\DashboardController;
use App\Http\Controllers\Crm\FacebookWebhookController;
use App\Http\Controllers\Crm\ImportController;
use App\Http\Controllers\Crm\LeadActionController;
use App\Http\Controllers\Crm\LeadCaptureFormController;
use App\Http\Controllers\Crm\LeadController;
use App\Http\Controllers\Crm\MessageTemplateController;
use App\Http\Controllers\Crm\ProjectController;
use App\Http\Controllers\Crm\PropertyController;
use App\Http\Controllers\Crm\ReportController;
use App\Http\Controllers\Crm\SourceController;
use App\Http\Controllers\Crm\TeamController;
use Illuminate\Support\Facades\Route;

Route::name('crm.')->group(function () {
    // Website enquiries (public). The ready-made form page, and the link other websites post to.
    // CSRF is skipped for api/capture/* and api/facebook/* in VerifyCsrfToken.
    Route::get('/f/{token}', [CaptureController::class, 'form'])->name('capture.form');
    Route::post('/f/{token}', [CaptureController::class, 'store'])->middleware('throttle:20,1')->name('capture.submit');
    Route::get('/f/{token}/thanks', [CaptureController::class, 'thanks'])->name('capture.thanks');
    Route::post('/api/capture/{token}', [CaptureController::class, 'store'])->middleware('throttle:30,1')->name('capture.store');

    // Facebook Lead Ads webhook
    Route::get('/api/facebook/leads', [FacebookWebhookController::class, 'verify'])->name('facebook.verify');
    Route::post('/api/facebook/leads', [FacebookWebhookController::class, 'receive'])->name('facebook.receive');

    Route::prefix('crm')->middleware(['auth', 'crm'])->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::post('/leads/assign', [LeadActionController::class, 'bulkAssign'])->name('leads.bulk-assign');
        Route::resource('leads', LeadController::class);
        Route::post('/leads/{lead}/stage', [LeadActionController::class, 'stage'])->name('leads.stage');
        Route::post('/leads/{lead}/call', [LeadActionController::class, 'call'])->name('leads.call');
        Route::post('/leads/{lead}/note', [LeadActionController::class, 'note'])->name('leads.note');
        Route::get('/leads/{lead}/whatsapp', [MessageTemplateController::class, 'send'])->name('leads.whatsapp');
        Route::post('/leads/{lead}/assign', [LeadActionController::class, 'assign'])->name('leads.assign');
        Route::post('/appointments/{appointment}', [LeadActionController::class, 'appointment'])->name('appointments.update');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');

        // Excel/CSV upload, including the Leadrat import (managers and admins; checked in the controller)
        Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');
        Route::post('/imports', [ImportController::class, 'store'])->name('imports.store');
        Route::get('/imports/template.csv', [ImportController::class, 'template'])->name('imports.template');
        Route::get('/imports/{import}', [ImportController::class, 'show'])->name('imports.show');
        Route::post('/imports/{import}/start', [ImportController::class, 'start'])->name('imports.start');
        Route::get('/imports/{import}/problems.csv', [ImportController::class, 'problems'])->name('imports.problems');

        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');

        // Admin settings. People, roles and managers are kept in the back office (Users).
        Route::middleware('crm.admin')->group(function () {
            Route::get('/team', [TeamController::class, 'index'])->name('team.index');
            Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
            Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
            Route::resource('properties', PropertyController::class)->except(['index', 'show', 'destroy']);
            Route::resource('capture-forms', LeadCaptureFormController::class)->except(['show', 'destroy']);
            Route::resource('assignment-rules', AssignmentRuleController::class)->except(['show']);
            Route::resource('message-templates', MessageTemplateController::class)->except(['show']);
            Route::get('/sources', [SourceController::class, 'index'])->name('sources.index');
            Route::post('/sources', [SourceController::class, 'store'])->name('sources.store');
            Route::put('/sources/{source}', [SourceController::class, 'update'])->name('sources.update');
            Route::post('/sources/{source}/sub-sources', [SourceController::class, 'storeSub'])->name('sub-sources.store');
            Route::put('/sub-sources/{subSource}', [SourceController::class, 'updateSub'])->name('sub-sources.update');
        });

        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    });
});
