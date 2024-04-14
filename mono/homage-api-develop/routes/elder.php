<?php

use App\Http\Controllers\v2\Elders\CallsController;
use App\Http\Controllers\v2\Elders\CasesController;
use App\Http\Controllers\v2\Elders\CentreResponsibleWorkerController;
use App\Http\Controllers\v2\Elders\DistrictController;
use App\Http\Controllers\v2\Elders\ElderController;
use App\Http\Controllers\v2\Elders\IndexEldersController;
use App\Http\Controllers\v2\Elders\MeetingNotesController;
use App\Http\Controllers\v2\Elders\ReferralController;
use App\Http\Controllers\v2\Elders\StaffUnitController;
use App\Http\Controllers\v2\Elders\ZoneController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', [IndexEldersController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum', 'auth.manager']], function () {
    Route::apiResource('districts', DistrictController::class);
    Route::apiResource('zones', ZoneController::class);
    Route::apiResource('staff-units', StaffUnitController::class);
    Route::apiResource('centres', CentreResponsibleWorkerController::class);
    Route::apiResource('referrals', ReferralController::class);
    Route::apiResource('cases', CasesController::class);
    Route::apiResource('elders', ElderController::class);
    Route::apiResource('meeting-notes', MeetingNotesController::class);
    Route::apiResource('calls', CallsController::class);

    Route::controller(ElderController::class)->group(function () {
        Route::post('elders-validation', 'elderValidation');
        Route::get('elders-autocomplete', 'autocomplete');
        Route::get('elders-backend-detail', 'elderDetail');
        Route::get('elders-cases', 'elderCases');
        Route::get('elders-calls', 'elderCalls');
        Route::get('elders-list', 'elderList');
        Route::post('elders-import', 'import');
        Route::get('elders-export', 'export');
        Route::get('elders-export-format', 'exportEnrollmentTemplate');
        Route::get('is-contact-number-available', 'isPhoneNumberAvailable');
        Route::post('elders-bulk-validation', 'bulkValidation');
        Route::post('elders-bulk-create', 'bulkCreate');
        Route::post('elders-export-invalid-data', 'exportInvalidData');
        Route::get('cases-status', 'getCasesStatus');
        Route::delete('uid/{uid}', 'destroyByUID');
    });
    Route::controller(DistrictController::class)->group(function () {
        route::post('/districts-import', 'import');
        route::get('/districts-export-format', 'exportFormat');
    });

    Route::controller(CasesController::class)->group(function () {
        Route::get('is-cases-id-exists', 'isCasesIdExists');
        Route::get('reports-export', 'exportPatientReport');
        Route::get('reports', 'reports');
    });

    Route::controller(CallsController::class)->group(function () {
        Route::get('staff-calls', 'staffCalls');
        Route::get('call-history-csv', 'exportCallHistory');
        Route::get('elder-calls', 'elderCalls');
    });

    Route::controller(MeetingNotesController::class)->group(function () {
        Route::get('meeting-notes-export', 'exportMeetingNotes');
    });
});
