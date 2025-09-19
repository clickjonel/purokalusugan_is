<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HrhController;
use App\Http\Controllers\PkpDisaggregationController;
use App\Http\Controllers\PkpEventsController;
use App\Http\Controllers\PkpIndicatorController;
use App\Http\Controllers\PkpIndicatorDisaggregationController;
use App\Http\Controllers\PkpIndicatorValuesController;
use App\Http\Controllers\ProgramsController;
use App\Http\Controllers\PkpRegionController;
use App\Http\Controllers\PkpProvinceController;
use App\Http\Controllers\PkpMunicipalityController;
use App\Http\Controllers\PkpBarangayController;
use App\Http\Controllers\GeoJsonController;
use App\Http\Controllers\PkpSiteController;
use App\Http\Controllers\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/geojson', [GeoJsonController::class, 'fetch']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthenticationController::class, 'login']);

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


// Team
Route::group([
    'prefix' => 'team'
], function () {
    Route::post('/create', [TeamController::class, 'create']);
    Route::get('/list', [TeamController::class, 'list']);
    Route::get('/find', [TeamController::class, 'getTeam']);
    Route::post('/member/create', [TeamController::class, 'saveMember']);
    Route::delete('/member/remove', [TeamController::class, 'removeMember']);
    Route::post('/scope/create', [TeamController::class, 'saveScope']);
    Route::delete('/scope/remove', [TeamController::class, 'removeScope']);
});

Route::group([
    'prefix' => 'indicator',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('/create', [PkpIndicatorController::class, 'createIndicator']);
    Route::get('/list', [PkpIndicatorController::class, 'getIndicators']);
    Route::get('/find', [PkpIndicatorController::class, 'getIndicator']);
    Route::post('/update', [PkpIndicatorController::class, 'updateIndicator']);
    Route::delete('/delete', [PkpIndicatorController::class, 'deleteIndicator']);
    Route::put('/status', [PkpIndicatorController::class, 'updateStatusOfProgram']);
    Route::delete('/disaggregation/remove', [PkpIndicatorController::class, 'removeDisaggregationOnIndicator']);
    Route::post('/disaggregation/add', [PkpIndicatorController::class, 'addIndicatorDisaggregations']);
});

Route::group([
    'prefix' => 'event',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('/create', [PkpEventsController::class, 'createEvent']);
    Route::get('/list', [PkpEventsController::class, 'list']);
    Route::put('/update', [PkpEventsController::class, 'updateEvent']);
    Route::delete('/delete', [PkpEventsController::class, 'deleteEvent']);    
    Route::post('/save', [PkpEventsController::class, 'saveEvent']);   
    Route::get('/fetch', [PkpEventsController::class, 'fetchEvent']);   
    Route::post('/populate', [PkpEventsController::class, 'populateEvent']);
});

Route::group([
    'prefix' => 'disaggregation',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('/create', [PkpDisaggregationController::class, 'createDisaggregation']);
    Route::get('/list', [PkpDisaggregationController::class, 'getDisaggregations']);
    Route::put('/update', [PkpDisaggregationController::class, 'updateDisaggregation']);
    Route::delete('/delete', [PkpDisaggregationController::class, 'deleteDisaggregation']); 
    Route::get('search/name',[PkpDisaggregationController::class,'searchDisaggregationByName']);
});

Route::group([
    'prefix' => 'indicator/value',
    // 'middleware' => 'auth:sanctum'
], function () {
    Route::post('/create', [PkpIndicatorValuesController::class, 'createIndicatorValue']);
    Route::get('/list', [PkpIndicatorValuesController::class, 'getIndicatorValues']);
    Route::put('/update', [PkpIndicatorValuesController::class, 'updateIndicatorValue']);
    Route::delete('/delete', [PkpIndicatorValuesController::class, 'deleteIndicatorValue']);    
});
Route::group([
    'prefix' => 'indicator/disaggregation',
    // 'middleware' => 'auth:sanctum'
], function () {
    Route::post('/create', [PkpIndicatorDisaggregationController::class, 'createIndicatorDisaggregation']);
    Route::get('/list', [PkpIndicatorDisaggregationController::class, 'getIndicatorDiasaggregation']);
    Route::put('/update', [PkpIndicatorDisaggregationController::class, 'updateIndicatorDisaggregation']);
    Route::delete('/delete', [PkpIndicatorDisaggregationController::class, 'deleteIndicatorDisaggregation']);
});
//Team
Route::group([
    'prefix' => 'team'
], function () {
    Route::post('/create', [TeamController::class, 'create']);
    Route::get('/list', [TeamController::class, 'list']);
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
    Route::get('/region/find', [PkpProvinceController::class, 'getProvincesByRegionId']);
});



//PKP Municipality
Route::group([
    'prefix' => 'municipality',
], function () {
    Route::get('/list', [PkpMunicipalityController::class, 'getMunicipalities']);
    Route::get('/find', [PkpMunicipalityController::class, 'getMunicipality']);
    Route::get('/region/find', [PkpMunicipalityController::class, 'getMunicipalitiesByRegionId']);
    Route::get('/province/find', [PkpMunicipalityController::class, 'getMunicipalitiesByProvinceId']);
});

//PKP Barangay
Route::group([
    'prefix' => 'barangay',
], function () {
    Route::get('/list', [PkpBarangayController::class, 'getBarangays']);
    Route::get('/find', [PkpBarangayController::class, 'getBarangay']);
    Route::get('/region/find', [PkpBarangayController::class, 'getBarangaysByRegionId']);
    Route::get('/province/find', [PkpBarangayController::class, 'getBarangaysByProvinceId']);
    Route::get('/municipality/find', [PkpBarangayController::class, 'getBarangaysByMunicipalityId']);
});

//PKP SITES
Route::group([
    'prefix' => 'site',
], function () {
    Route::post('/create', [PkpSiteController::class, 'createSite']);
    Route::get('/list', [PkpSiteController::class, 'getSites']);
    Route::get('/find', [PkpSiteController::class, 'getSite']);
    Route::put('/update', [PkpSiteController::class, 'updateSite']);
    Route::delete('/delete', [PkpSiteController::class, 'deleteSite']);
});

// dashboard
Route::get('/exec/dashboard', [DashboardController::class, 'getExecDashboardData']);
