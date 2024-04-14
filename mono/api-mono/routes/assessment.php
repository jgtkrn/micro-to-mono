<?php

use App\Http\Controllers\v2\Assessments\AppointmentController;
use App\Http\Controllers\v2\Assessments\AssessmentCaseController;
use App\Http\Controllers\v2\Assessments\AssessmentCaseFileController;
use App\Http\Controllers\v2\Assessments\BznCareTargetController;
use App\Http\Controllers\v2\Assessments\BznConsultationNotesController;
use App\Http\Controllers\v2\Assessments\CarePlanController;
use App\Http\Controllers\v2\Assessments\CaseManagerController;
use App\Http\Controllers\v2\Assessments\CgaCareTargetController;
use App\Http\Controllers\v2\Assessments\CgaConsultationNotesController;
use App\Http\Controllers\v2\Assessments\CoachingPamController;
use App\Http\Controllers\v2\Assessments\CommunityController;
use App\Http\Controllers\v2\Assessments\ConsultationNotesFileController;
use App\Http\Controllers\v2\Assessments\CrossDisciplinaryController;
use App\Http\Controllers\v2\Assessments\ElderProfile;
use App\Http\Controllers\v2\Assessments\FollowUpHistoryController;
use App\Http\Controllers\v2\Assessments\FormController;
use App\Http\Controllers\v2\Assessments\IndexAssessmentsController;
use App\Http\Controllers\v2\Assessments\MedicalHistoryController;
use App\Http\Controllers\v2\Assessments\MedicationDrugController;
use App\Http\Controllers\v2\Assessments\MedicationHistoryController;
use App\Http\Controllers\v2\Assessments\PreCoachingPamController;
use App\Http\Controllers\v2\Assessments\ReportsDashboardController;
use App\Http\Controllers\v2\Assessments\SatisfactionEvaluationFormController;
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

Route::get('/', [IndexAssessmentsController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum', 'auth.manager']], function () {
    Route::apiResource('medication-drugs', MedicationDrugController::class);
    Route::apiResource('medical-histories', MedicalHistoryController::class);
    Route::apiResource('appointments', AppointmentController::class);
    Route::apiResource('follow-up-histories', FollowUpHistoryController::class);
    Route::apiResource('medication-histories', MedicationHistoryController::class);
    Route::apiResource('assessment-cases', AssessmentCaseController::class);
    Route::apiResource('assessment-case-forms', FormController::class);
    Route::apiResource('bzn-care-targets', BznCareTargetController::class);
    Route::apiResource('cga-care-targets', CgaCareTargetController::class);
    Route::apiResource('satisfaction-evaluation', SatisfactionEvaluationFormController::class);
    Route::apiResource('cross-disciplinary', CrossDisciplinaryController::class);
    Route::apiResource('communities', CommunityController::class);
    Route::apiResource('care-plans', CarePlanController::class);
    Route::apiResource('bzn-consultation', BznConsultationNotesController::class);
    Route::apiResource('cga-consultation', CgaConsultationNotesController::class);
    Route::apiResource('coaching-pam', CoachingPamController::class);
    Route::apiResource('pre-coaching-pam', PreCoachingPamController::class);
    Route::controller(CoachingPamController::class)->group(function () {
        Route::put('coaching-pam', 'update');
    });
    Route::controller(PreCoachingPamController::class)->group(function () {
        Route::put('pre-coaching-pam', 'update');
    });

    Route::controller(ConsultationNotesFileController::class)->group(function () {
        Route::get('consultation-notes-files/{id}', 'download');
        Route::delete('consultation-notes-files/{id}', 'destroy');
        Route::post('consultation-sign-files/{id}', 'upsertSign');
        Route::post('consultation-attachment-files/{id}', 'upsertAttachment');
    });
    Route::controller(AssessmentCaseFileController::class)->group(function () {
        Route::post('assessment-case-files', 'upload');
        Route::get('assessment-case-files/{id}', 'download');
        Route::delete('assessment-case-files/{id}', 'destroy');
    });
    Route::controller(CaseManagerController::class)->group(function () {
        Route::get('assigned-case-managers', 'index');
        Route::put('assigned-case-managers', 'update');
    });

    Route::controller(CarePlanController::class)->group(function () {
        Route::get('care-plans-report', 'reportsResourceSet');
        Route::get('staff-care-plans', 'reportsResourceStaffSet');
        Route::get('case-manager', 'checkCarePlanCaseManager');
        Route::get('case-manager-set', 'caseManagerByCasesSet');
        Route::get('case-status', 'getCaseStatus');
    });

    Route::controller(CgaCareTargetController::class)->group(function () {
        Route::post('cga-care-targets-rev', 'storeV2');
        Route::get('coaching-goal-csv', 'exportHCG');
    });

    Route::controller(BznConsultationNotesController::class)->group(function () {
        Route::get('omaha-plan-export', 'exportPlans');
        Route::get('omaha-vital-export', 'exportVitalSigns');
    });

    Route::controller(MedicalHistoryController::class)->group(function () {
        Route::post('medical-histories/search', 'search');
        Route::get('medical-histories/case-id/{case_id}', 'getByCaseId');
    });

    Route::controller(MedicationDrugController::class)->group(function () {
        Route::post('medication-drugs/search', 'search');
        Route::post('medication-drugs-import', 'import');
    });

    Route::get('medication-histories/case-id/{case_id}', [MedicationHistoryController::class, 'getByCaseId']);
    Route::post('appointments/search', [AppointmentController::class, 'search']);
    Route::get('follow-up-histories/case-id/{case_id}', [FollowUpHistoryController::class, 'getByCaseId']);
    Route::get('teams-check', [AssessmentCaseController::class, 'checkTeams']);
    Route::get('elder-profile', [ElderProfile::class, 'index']);
    Route::get('reports/insight', [ReportsDashboardController::class, 'indexInsight']);
    Route::get('coaching-session-csv', [CgaConsultationNotesController::class, 'exportHCS']);
    Route::post('bzn-care-targets-rev', [BznCareTargetController::class, 'storeV2']);
});
