<?php

use App\Http\Controllers\Call\CallsController;
use App\Http\Controllers\Case\CasesController;
use App\Http\Controllers\District\DistrictController;
use App\Http\Controllers\Elder\ElderController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\CentreResponsibleWorkerController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\MeetingNotesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoggerController;

Route::prefix('v1')->group(function () {
    Route::apiResource('cases', CasesController::class, ['only' => ['index', 'show', 'isCasesIdExists']]);
    Route::apiResource('elders', ElderController::class, ['except' => ['store', 'update']]);
    Route::post('elders-validation', [ElderController::class, 'elderValidation']);
    Route::apiResource('meeting-notes', MeetingNotesController::class, ['only' => ['index', 'show']]);
    Route::group(['middleware' => 'external.auth'], function () {
        Route::apiResource('elders', ElderController::class, ['only' => ['store']])->middleware('role.nothelper');
        Route::apiResource('elders', ElderController::class, ['only' => ['update']])->middleware('role.admin', 'role.nothelper');
        Route::apiResource('calls', CallsController::class)->middleware('role.all');
        Route::apiResource('cases', CasesController::class, ['except' => ['index', 'show', 'isCasesIdExists']]);
        Route::apiResource('meeting-notes', MeetingNotesController::class, ['except' => ['index', 'show']]);
    });
    Route::apiResource('districts', DistrictController::class);
    Route::apiResource('zones', ZoneController::class);
    Route::apiResource('centres', CentreResponsibleWorkerController::class);
    Route::apiResource('referrals', ReferralController::class);
    Route::controller(ElderController::class)->group(function () {
        Route::get('/elders-autocomplete', 'autocomplete');
        Route::get('/elders-backend-detail', 'elderDetail')->middleware('external.auth', 'role.all');
        Route::get('/elders-cases', 'elderCases');
        Route::get('/elders-calls', 'elderCalls');
        Route::get('/elders-list', 'elderList')->middleware('external.auth', 'role.all');
        Route::post('/elders-import', 'import');
        Route::get('/elders-export', 'export')->middleware('external.auth', 'role.adminmanager', 'role.nothelper');
        Route::get('/elders-export-format', 'exportEnrollmentTemplate');
        Route::get('/is-contact-number-available', 'isPhoneNumberAvailable');
        Route::post('/elders-bulk-validation', 'bulkValidation');
        Route::post('/elders-bulk-create', 'bulkCreate')->middleware('external.auth', 'role.admin', 'role.nothelper');
        Route::post('/elders-export-invalid-data', 'exportInvalidData');
    });
    Route::controller(DistrictController::class)->group(function () {
        route::post('/districts-import', 'import');
        route::get('/districts-export-format', 'exportFormat');
    });
    Route::get('is-cases-id-exists', [CasesController::class, 'isCasesIdExists']);
    Route::get('/reports', [CasesController::class, 'reports'])->middleware('external.auth');
    Route::get('staff-calls', [CallsController::class, 'staff_calls']);
    Route::get('/debug-sentry', function () {
        throw new Exception('Sentry working properly in Elder Service!');
    });

    Route::get('elder-event', [ElderController::class, 'elderEventResourceSet']);
    Route::get('many-elder-event', [ElderController::class, 'elderEventManyResourceSet']);
    Route::get('/load-logger', [LoggerController::class, 'index']);
    Route::get('/meeting-notes-export', [MeetingNotesController::class, 'exportMeetingNotes'])->middleware('external.auth');
    Route::get('/cases-uid-set', [CasesController::class, 'getCasesUidSet']);
    Route::get('/cases-status', [ElderController::class, 'getCasesStatus']);
    Route::get('/call-history-csv', [CallsController::class, 'exportCallHistory']);
    Route::get('/elder-calls', [CallsController::class, 'elder_calls']);
    Route::get('/reports-export', [CasesController::class, 'exportPatientReport']);
    Route::get('/uid-set-by-cases-id', [CasesController::class, 'getUidSetByCasesId']);
});
