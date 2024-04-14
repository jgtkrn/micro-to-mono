<?php

use App\Http\Controllers\v2\Appointments\AppointmentController;
use App\Http\Controllers\v2\Appointments\FileController;
use App\Http\Controllers\v2\Appointments\IndexAppointmentsController;
use App\Http\Controllers\v2\Appointments\WebhookController;
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

Route::get('/', [IndexAppointmentsController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum', 'auth.manager']], function () {
    Route::controller(AppointmentController::class)->group(function () {
        Route::get('appointments', 'index');
        Route::get('appointments/events', 'calendar');
        Route::post('appointments', 'store');
        Route::put('appointments/{id}', 'update');
        Route::delete('appointments/{id}', 'destroy');
        Route::delete('appointments', 'massDestroy');
        Route::get('appointments-csv', 'exportCsv');
        Route::get('appointments-export', 'exportAppointments');
        Route::get('appointments/{id}', 'newDetails');
        Route::get('appointments-today', 'getTodayUsers');
    });
    Route::controller(FileController::class)->group(function () {
        Route::post('appointments/files', 'upload');
        Route::get('appointments/files/{id}', 'download');
    });
    Route::post('webhook', [WebhookController::class, 'store']);
});
