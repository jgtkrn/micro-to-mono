<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserCapacitorController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LoggerController;
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

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::get('/user', 'authUser');
        Route::post('/login', 'login')->name('login');
        Route::post('/logout', 'logout')->name('logout');
        Route::post('/password/forgot', 'forgotPassword')->name('password.forgot');
        Route::get('/password/reset', 'getResetPassword')->name('password.reset');
        Route::post('/password/reset', 'resetPassword')->name('password.reset');
    });
    Route::get('/users/autocomplete', [UserController::class, 'autocomplete']);

    Route::get('/users/autocompletenew', [UserController::class, 'autocompletenew']);

    Route::get('/check-email', [UserController::class, 'userByEmail']);

    Route::apiResource('user-capacitor', UserCapacitorController::class);
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('roles', RoleController::class);
    Route::group(['middleware' => 'external.auth'], function(){
        Route::group(['middleware' => 'role.all', 'role.nothelper'], function(){
            Route::apiResource('users', UserController::class, ['only' => ['index', 'show']]);
        });
        Route::group(['middleware' => 'role.admin'], function(){
            Route::apiResource('users', UserController::class, ['only' => ['store', 'destroy']]);
            Route::get('reports', [UserController::class, 'reports']);
        });
        Route::group(['middleware' => 'role.adminmanager'], function(){
            Route::apiResource('users', UserController::class, ['only' => ['update']]);
        });
    });
    Route::get('/user-event', [UserController::class, 'getUsersFromEvent']);
    Route::apiResource('groups', GroupController::class);
    Route::get('/debug-sentry', function () {
        throw new Exception('Sentry working properly in User Service!');
    });
    Route::get('health', HealthCheckJsonResultsController::class);
    Route::get('/load-logger', [LoggerController::class, 'index']);
    Route::get('/users-set', [UserController::class, 'getUsersSet']);
    Route::get('/users-report-csv', [UserController::class, 'exportUserReport']);
});

