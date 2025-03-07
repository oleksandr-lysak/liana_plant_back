<?php

use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\MasterController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\SmsVerificationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return true;
});
Route::get('/get-saved-tokens', function () {
    $tokenService = new \App\Http\Services\FcmTokenService;

    return $tokenService->getTokensForMasters([1001]);
});

Route::prefix('masters')->group(function () {
    Route::get('/', [MasterController::class, 'index']);
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/review', [MasterController::class, 'addReview']);
    });
    Route::get('/{id}', [MasterController::class, 'getMaster']);
    Route::post('/{id}/work-schedule', [MasterController::class, 'updateWorkSchedule']);
});

Route::prefix('time-slots')->group(function () {
    Route::post('/book', [MasterController::class, 'bookTimeSlot']);
    Route::post('/free', [MasterController::class, 'setFreeTimeSlot']);
    Route::post('/book-in-redis', [MasterController::class, 'bookTimeInRedis']);
});

Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::get('/{id}', [ServiceController::class, 'getService']);
    Route::get('/get-for-master/{master_id}', [ServiceController::class, 'getServicesForMaster']);
});

Route::prefix('auth')->group(function () {
    Route::post('/master-register',
        [MasterController::class, 'verifyAndRegister']
    );
    Route::post('/client-register',
        [ClientController::class, 'register']
    );
    Route::post('/send-code', [SmsVerificationController::class, 'sendCode']);
    Route::post('/verify-code', [UserController::class, 'verifyCode']);
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/protected-route', [MasterController::class, 'protectedMethod']);
});
