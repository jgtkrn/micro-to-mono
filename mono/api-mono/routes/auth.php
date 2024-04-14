<?php

use App\Http\Controllers\v2\Auth\AuthController;
use App\Http\Controllers\v2\Auth\IndexAuthController;
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

Route::get('/', [IndexAuthController::class, 'index']);

Route::controller(AuthController::class)->group(function () {
    Route::get('user', 'authUser')->middleware('auth:sanctum');
    Route::post('login', 'login')->name('login');
    Route::post('logout', 'logout')->name('logout')->middleware('auth:sanctum');
    Route::post('password/forgot', 'forgotPassword')->name('password.forgot');
    Route::get('password/reset', 'getResetPassword')->name('password.reset');
    Route::post('password/reset', 'resetPassword')->name('password.reset');
});
