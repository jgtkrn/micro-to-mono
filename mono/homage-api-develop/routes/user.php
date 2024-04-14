<?php

use App\Http\Controllers\v2\Users\GroupController;
use App\Http\Controllers\v2\Users\IndexUserController;
use App\Http\Controllers\v2\Users\RoleController;
use App\Http\Controllers\v2\Users\TeamController;
use App\Http\Controllers\v2\Users\UserCapacitorController;
use App\Http\Controllers\v2\Users\UserController;
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

Route::get('/', [IndexUserController::class, 'index']);
Route::group(['middleware' => ['auth:sanctum', 'auth.manager']], function () {
    Route::apiResource('groups', GroupController::class);
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('user-capacitor', UserCapacitorController::class);
    Route::controller(UserController::class)->group(function () {
        Route::get('autocomplete', 'autocomplete');
        Route::get('autocompletenew', 'autocompletenew');
        Route::get('reports', 'reports');
        Route::get('users-report-csv', 'exportUserReport');
        Route::get('users-list-csv', 'exportUserList');
        Route::get('check-email', 'userByEmail');
    });
});
