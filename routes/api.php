<?php

use Illuminate\Http\Request;
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

Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);


// verify_email middleware is custom middleware
// that I created to check if Authenticated user is verified or not

Route::group([
    'middleware' => ['api', 'verify_email'],
    'prefix' => 'auth'
], function () {

    // To-Do Routes
    Route::resource('todos', \App\Http\Controllers\Api\TodoController::class);

    //JWT ROUTES
    Route::post('refresh', [\App\Http\Controllers\Api\AuthController::class, 'refresh']);

    //OTP SERVICE ROUTES
    Route::post('verify-otp', [\App\Http\Controllers\Api\AuthController::class, 'verifyOtp']);

    // Logout route
    Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
});
