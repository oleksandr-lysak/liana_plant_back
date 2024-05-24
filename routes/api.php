<?php

use App\Http\Controllers\MasterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestController;
use App\Http\Controllers\SpecialityController;

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
    Route::post('/', [MasterController::class, 'addMaster']);
    Route::get('/{id}', [MasterController::class, 'getMaster']);
});

Route::prefix('specialties')->group(function () {
    Route::get('/', [SpecialityController::class, 'index']);
});

Route::prefix('specialities')->group(function () {
    Route::get('/', [SpecialityController::class, 'index']);
    Route::get('/{id}', [SpecialityController::class, 'getSpeciality']);
});

