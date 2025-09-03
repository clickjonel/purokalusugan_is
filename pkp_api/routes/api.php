<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ProgramsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/program/create', [ProgramsController::class, 'create']);


Route::post('/login',[AuthenticationController::class,'login']);