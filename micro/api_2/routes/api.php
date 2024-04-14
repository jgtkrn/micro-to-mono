<?php

use App\Http\Controllers\AssessmentCaseController;
use App\Http\Controllers\AssessmentCaseFileController;
use App\Http\Controllers\BznCareTargetController;
use App\Http\Controllers\CgaCareTargetController;
use App\Http\Controllers\CarePlanController;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use App\Http\Controllers\BznConsultationNotesController;
use App\Http\Controllers\CgaConsultationNotesController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MedicationDrugController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\MedicationHistoryController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\FollowUpHistoryController;
use App\Http\Controllers\ConsultationNotesFileController;
use App\Http\Controllers\ReportsDashboardController;
use App\Http\Controllers\CoachingPamController;
use App\Http\Controllers\PreCoachingPamController;
use App\Http\Controllers\ElderProfile;
use App\Http\Controllers\SatisfactionEvaluationFormController;
use App\Http\Controllers\CrossDisciplinaryController;
use App\Http\Controllers\LoggerController;
use App\Http\Controllers\CaseManagerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('medication-drugs', MedicationDrugController::class);
Route::apiResource('medical-histories', MedicalHistoryController::class);

Route::get('health', HealthCheckJsonResultsController::class);
Route::post('medication-drugs/search', [MedicationDrugController::class, 'search']);
Route::post('medical-histories/search', [MedicalHistoryController::class, 'search']);
Route::get('medical-histories/case-id/{case_id}', [MedicalHistoryController::class, 'getByCaseId']);

Route::group(['middleware' => 'external.auth'], function(){
    Route::post('appointments/search', [AppointmentController::class, 'search']);
    Route::get('follow-up-histories/case-id/{case_id}', [FollowUpHistoryController::class, 'getByCaseId'])->middleware('role.nothelper');
    Route::get('medication-histories/case-id/{case_id}', [MedicationHistoryController::class, 'getByCaseId'])->middleware('role.nothelper');

    Route::apiResource('appointments', AppointmentController::class);
    Route::apiResource('follow-up-histories', FollowUpHistoryController::class)->middleware('role.nothelper');
    Route::apiResource('medication-histories', MedicationHistoryController::class)->middleware('role.nothelper');
    Route::apiResource('assessment-cases', AssessmentCaseController::class, ['only' => ['index', 'show']]);
    Route::apiResource('assessment-case-forms', FormController::class, ['only' => ['show']]);
    Route::apiResource('assessment-cases', AssessmentCaseController::class, ['except' => ['index', 'show']]);
    Route::apiResource('assessment-case-forms', FormController::class, ['except' => ['show']]);
    Route::apiResource('bzn-care-targets', BznCareTargetController::class, ['only' => ['index', 'show']])->middleware('role.nothelper');
    Route::apiResource('cga-care-targets', CgaCareTargetController::class, ['only' => ['index', 'show']])->middleware('role.nothelper');
    Route::apiResource('bzn-care-targets', BznCareTargetController::class, ['except' => ['index', 'show']]);
    Route::apiResource('cga-care-targets', CgaCareTargetController::class, ['except' => ['index', 'show']]);
    Route::post('bzn-care-targets-rev', [BznCareTargetController::class, 'storeV2'])->middleware('role.nothelper');
    Route::post('cga-care-targets-rev', [CgaCareTargetController::class, 'storeV2'])->middleware('role.nothelper');
    Route::apiResource('bzn-consultation', BznConsultationNotesController::class, ['only' => ['index']])->middleware('role.nothelper');
    Route::apiResource('cga-consultation', CgaConsultationNotesController::class, ['only' => ['index']])->middleware('role.nothelper');
    Route::controller(ConsultationNotesFileController::class)->group(function () {
        Route::get('/consultation-notes-files/{id}', 'download');
        Route::delete('/consultation-notes-files/{id}', 'destroy');
        Route::post('/consultation-sign-files/{id}', 'upsertSign');
        Route::post('/consultation-attachment-files/{id}', 'upsertAttachment');
    });
    Route::apiResource('bzn-consultation', BznConsultationNotesController::class, ['except' => ['index']]);
    Route::apiResource('cga-consultation', CgaConsultationNotesController::class, ['except' => ['index']]);
    Route::get('reports/staff', [ReportsDashboardController::class, 'index_staff']);
    Route::get('reports/patient', [ReportsDashboardController::class, 'index_patient']);
    Route::get('reports/insight', [ReportsDashboardController::class, 'index_insight']);
    Route::controller(AssessmentCaseFileController::class)->group(function () {
        Route::post('/assessment-case-files', 'upload');
        Route::get('/assessment-case-files/{id}', 'download');
        Route::delete('/assessment-case-files/{id}', 'destroy');
    });
    Route::apiResource('satisfaction-evaluation', SatisfactionEvaluationFormController::class);
    Route::apiResource('cross-disciplinary', CrossDisciplinaryController::class)->middleware('role.nothelper');
    Route::apiResource('communities', CommunityController::class, ['only' => ['index', 'show']])->middleware('role.nothelper');
    // Route::apiResource('care-plans', CarePlanController::class, ['only' => ['index', 'show']]);
    Route::apiResource('communities', CommunityController::class, ['except' => ['index', 'show']])->middleware('role.nothelper');
    Route::apiResource('care-plans', CarePlanController::class, ['only' => ['index', 'show']])->middleware('role.nothelper');
    Route::apiResource('care-plans', CarePlanController::class, ['except' => ['index', 'show']])->middleware('role.nothelper');
    Route::get('teams-check', [AssessmentCaseController::class, 'checkTeams']);
    Route::get('assigned-case-managers', [CaseManagerController::class, 'index'])->middleware('role.nothelper');
    Route::put('assigned-case-managers', [CaseManagerController::class, 'update'])->middleware('role.adminmanager', 'role.nothelper');
    Route::get('coaching-pam', [CoachingPamController::class, 'index']);
    Route::get('coaching-pam/{id}', [CoachingPamController::class, 'show']);
    Route::put('coaching-pam', [CoachingPamController::class, 'update']);
    Route::delete('coaching-pam/{id}', [CoachingPamController::class, 'destroy']);

    Route::get('pre-coaching-pam', [PreCoachingPamController::class, 'index']);
    Route::get('pre-coaching-pam/{id}', [PreCoachingPamController::class, 'show']);
    Route::put('pre-coaching-pam', [PreCoachingPamController::class, 'update']);
    Route::delete('pre-coaching-pam/{id}', [PreCoachingPamController::class, 'destroy']);
    Route::get('elder-profile', [ElderProfile::class, 'index']);
});
Route::get('care-plans-report', [CarePlanController::class, 'reportsResourceSet']);
Route::get('staff-care-plans', [CarePlanController::class, 'reportsResourceStaffSet']);
Route::get('case-manager', [CarePlanController::class, 'checkCarePlanCaseManager']);
Route::get('/debug-sentry', function () {
    throw new Exception('Sentry working properly in Assessment Service!');
});
Route::get('/load-logger', [LoggerController::class, 'index']);
Route::get('/omaha-plan-export', [BznConsultationNotesController::class, 'exportPlans']);
Route::get('/omaha-vital-export', [BznConsultationNotesController::class, 'exportVitalSigns']);
Route::get('/case-manager-set', [CarePlanController::class, 'caseManagerByCasesSet']);
Route::get('/case-status', [CarePlanController::class, 'getCaseStatus']);
Route::get('/coaching-goal-csv', [CgaCareTargetController::class, 'exportHCG']);
Route::get('/coaching-session-csv', [CgaConsultationNotesController::class, 'exportHCS']);
