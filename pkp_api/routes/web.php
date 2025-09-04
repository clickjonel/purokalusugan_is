<?php

// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return file_get_contents(public_path('pkp/index.html'));
})->where('any', '.*');