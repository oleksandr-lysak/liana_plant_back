<?php

use App\Http\Controllers\MasterController;
use App\Http\Controllers\SmsVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\SpecialityController;
use App\Http\Controllers\UserController;

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


Route::get('/', function (){
    return true;
});

Route::prefix('masters')->group(function () {
    Route::get('/', [MasterController::class, 'index']);
    Route::post('/review', [MasterController::class, 'addReview']);
    Route::get('/{id}', [MasterController::class, 'getMaster']);
});

Route::prefix('specialties')->group(function () {
    Route::get('/', [SpecialityController::class, 'index']);
    Route::get('/{id}', [SpecialityController::class, 'getSpeciality']);
    Route::get('/get-for-master/{master_id}', [SpecialityController::class,'getSpecialitiesForMaster']);
});

Route::prefix('auth')->group(function () {
    Route::post('/master-register',
        [MasterController::class, 'verifyAndRegister']
    );
    Route::post('/send-code', [SmsVerificationController::class, 'sendCode']);
    Route::post('/verify-code', [UserController::class, 'verifyCode']);
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/protected-route', [MasterController::class, 'protectedMethod']);
});

Route::post('/time-slots', [TimeSlotController::class, 'store']);
Route::get('/time-slots/{start_date}/{masterId}', [TimeSlotController::class, 'index']);

