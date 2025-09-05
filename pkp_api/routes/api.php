<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\PkpIndicatorController;
use App\Http\Controllers\ProgramsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::post('/program/create', [ProgramsController::class, 'create']);
// Route::get('/programs', [ProgramsController::class, 'list']);

Route::post('/login',[AuthenticationController::class,'login']);

//Indicators
Route::group([
    'prefix' => 'indicator'
], function () {
    Route::post('/create',[PkpIndicatorController::class,'createIndicator']);
    Route::get('/list', [PkpIndicatorController::class, 'getIndicators']);
    Route::get('/find', [PkpIndicatorController::class, 'getIndicator']);
    Route::put('/update', [PkpIndicatorController::class, 'updateIndicator']);
    Route::delete('/delete', [PkpIndicatorController::class, 'deleteIndicator']);
});

//Programs
Route::group([
    'prefix' => 'program'
], function () {
    Route::post('/create',[ProgramsController::class,'createProgram']);
    Route::get('/list', [ProgramsController::class, 'getPrograms']);
    Route::get('/find', action: [ProgramsController::class, 'getProgram']);
    Route::put('/update', [ProgramsController::class, 'updateProgram']);
    Route::delete('/delete', [ProgramsController::class, 'deleteProgram']);
});


//HRH User
Route::group([
    'prefix' => 'hrh',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('/create',[ProgramsController::class,'createHrhUser']);
});