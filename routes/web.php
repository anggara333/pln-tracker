<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckController;

Route::get('/', function () { return view('welcome'); });
Route::get('/', [CheckController::class, 'index']);
Route::resource('check', CheckController::class);


