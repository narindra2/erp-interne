<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SampleDataController;
use App\Http\Controllers\SlackEndpointController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

// Sample API route
Route::get('/profits', [SampleDataController::class, 'profits'])->name('profits');

Route::post('/register', [RegisteredUserController::class, 'apiStore']);

Route::get('/login', [AuthenticatedSessionController::class, 'apiStore']);

Route::get('/forgot_password', [PasswordResetLinkController::class, 'apiStore']);

Route::get('/verify_token', [AuthenticatedSessionController::class, 'apiVerifyToken']);

Route::get('/users', [SampleDataController::class, 'getUsers']);


Route::get('/users', [SampleDataController::class, 'getUsers']);

Route::get('/slack/endpoint', [SlackEndpointController::class, 'capture_get']);
Route::post('/slack/endpoint', [SlackEndpointController::class, 'capture']);
Broadcast::routes(['middleware' => ['auth:sanctum']]);