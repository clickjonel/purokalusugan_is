<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\HrhController;
use App\Http\Controllers\PkpIndicatorController;
use App\Http\Controllers\ProgramsController;
use App\Http\Controllers\PkpRegionController;
use App\Http\Controllers\PkpProvinceController;
use App\Http\Controllers\PkpMunicipalityController;
use App\Http\Controllers\PkpBarangayController;
use App\Http\Controllers\GeoJsonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/geojson', [GeoJsonController::class, 'fetch']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::post('/program/create', [ProgramsController::class, 'create']);
// Route::get('/programs', [ProgramsController::class, 'list']);

Route::post('/login', [AuthenticationController::class, 'login']);

//Programs
Route::group([
    'prefix' => 'program',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('/create', [ProgramsController::class, 'createProgram']);
    Route::get('/list', [ProgramsController::class, 'getPrograms']);
    Route::get('/find',  [ProgramsController::class, 'getProgram']);
    Route::put('/update', [ProgramsController::class, 'updateProgram']);
    Route::delete('/delete', [ProgramsController::class, 'deleteProgram']);
    Route::put('/status', [ProgramsController::class, 'updateStatusOfProgram']);
});

//Indicators
Route::group([
    'prefix' => 'indicator'
], function () {
    Route::post('/create', [PkpIndicatorController::class, 'createIndicator']);
    Route::get('/list', [PkpIndicatorController::class, 'getIndicators']);
    Route::get('/find', [PkpIndicatorController::class, 'getIndicator']);
    Route::put('/update', [PkpIndicatorController::class, 'updateIndicator']);
    Route::delete('/delete', [PkpIndicatorController::class, 'deleteIndicator']);
});

//HRH User
Route::group([
    'prefix' => 'hrh',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('/create', [HrhController::class, 'createHrhUser']);
    Route::get('/list', [HrhController::class, 'getHrhList']);
});

//PKP Region
Route::group([
    'prefix' => 'region',
], function () {
    Route::get('/list', [PkpRegionController::class, 'getRegions']);
    Route::get('/find', [PkpRegionController::class, 'getRegion']);
});

//PKP Province
Route::group([
    'prefix' => 'province',
], function () {
    Route::get('/list', [PkpProvinceController::class, 'getProvinces']);
    Route::get('/find', [PkpProvinceController::class, 'getProvince']);
});

//PKP Municipality
Route::group([
    'prefix' => 'municipality',
], function () {
    Route::get('/list', [PkpMunicipalityController::class, 'getMunicipalities']);
    Route::get('/find', [PkpMunicipalityController::class, 'getMunicipality']);
});

//PKP Barangay
Route::group([
    'prefix' => 'barangay',
], function () {
    Route::get('/list', [PkpBarangayController::class, 'getBarangays']);
    Route::get('/find', [PkpBarangayController::class, 'getBarangay']);
});
