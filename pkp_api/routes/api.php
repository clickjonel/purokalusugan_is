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
    Route::get('/', [PkpIndicatorController::class, 'getIndicators']);
    Route::get('/{indicator_id}', [PkpIndicatorController::class, 'getIndicator']);
    Route::put('/{indicator_id}', [PkpIndicatorController::class, 'updateIndicator']);
    Route::delete('/{indicator_id}', [PkpIndicatorController::class, 'deleteIndicator']);
});

//Programs
Route::group([
    'prefix' => 'program'
], function () {
    Route::post('/create',[ProgramsController::class,'createProgram']);
    Route::get('/', [ProgramsController::class, 'getPrograms']);
    Route::get('/{program_id}', action: [ProgramsController::class, 'getProgram']);
    Route::put('/{program_id}', [ProgramsController::class, 'updateProgram']);
    Route::delete('/{program_id}', [ProgramsController::class, 'deleteProgram']);
});