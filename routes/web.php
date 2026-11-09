<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\HarianController;

Route::get('/', [CheckController::class, 'index'])->name('home');

Route::post('/check/bulk-delete', [CheckController::class, 'bulkDelete'])->name('check.bulkDelete');
Route::get('/check/export-pdf', [CheckController::class, 'exportPdf'])->name('check.exportPdf');

Route::post('/harian/bulk-delete', [HarianController::class, 'bulkDelete'])->name('harian.bulkDelete');
Route::get('/harian/export-pdf', [HarianController::class, 'exportPdf'])->name('harian.exportPdf');

Route::resource('check', CheckController::class);

Route::get('/harian', [HarianController::class, 'index'])->name('harian.index');
Route::post('/harian', [HarianController::class, 'store'])->name('harian.store');
Route::delete('/harian/{id}', [HarianController::class, 'destroy'])->name('harian.destroy');