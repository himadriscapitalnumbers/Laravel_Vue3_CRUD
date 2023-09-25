<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;

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

Route::prefix('auth')->group(function () {

    Route::post('/login', LoginController::class);
    Route::post('/logout', LogoutController::class)->middleware('auth:sanctum');
    Route::post('/register', RegisterController::class);

    Route::post('/forgotpassword', [ForgotPasswordController::class, 'createResetPasswordToken']);
    Route::post('/validate-password-token', [ForgotPasswordController::class, 'validatePasswordToken']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetpassword']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); 

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/events', EventsController::class);
    
    Route::get('/events-exporttocsv', [EventsController::class, 'exporttocsv']);
    Route::get('/events-exporttoics', [EventsController::class, 'exporttoics']);
    Route::post('/events-import', [EventsController::class, 'importevents']);
});
