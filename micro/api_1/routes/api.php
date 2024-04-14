<?php

use App\Http\Controllers\Api\v1\AppointmentController;
use App\Http\Controllers\Api\v1\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\CalendarController;
use App\Http\Controllers\Api\v1\FileController;
use App\Http\Controllers\Api\v1\LoggerController;

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

// Route::post("/login", [AuthController::class, "login"]);

// Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => ['external.auth']], function () {

    Route::controller(AppointmentController::class)->group(function () {
        Route::get("/appointments", "index");
        Route::get("/appointments/events", "calendar");
        Route::post("/appointments", "store");
        Route::put("/appointments/{id}", "update");
        Route::delete("/appointments/{id}", "destroy");
        Route::delete("/appointments", "massDestroy");
        Route::get("/appointments-csv", "exportCsv");
        Route::get("/appointments-export", "exportAppointments");
    });

    Route::controller(FileController::class)->group(function () {
        Route::post("/appointments/files", "upload");
        Route::get("/appointments/files/{id}", "download");
    });
});
Route::get("/appointments-today", [AppointmentController::class, "getTodayUsers"]);
Route::post("/webhook", [WebhookController::class, 'store']);
// Route::get("/appointments-leave", [AppointmentController::class, "getLeave"]);
Route::get("/debug-sentry", function () {
    throw new Exception('Sentry working properly in Appointment Service!');
});

Route::get("/appointments-report", [AppointmentController::class, "reportResourceSet"]);
Route::get("/staff-appointments", [AppointmentController::class, "staffReportRecordSet"]);

Route::get("/appointments/{id}", [AppointmentController::class,"newDetails"]);
Route::get('/load-logger', [LoggerController::class, 'index']);
Route::get('/elder-appointments', [AppointmentController::class, 'elderReportRecordSet']);