<?php

use App\Http\Controllers\ExcelImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/import', [ExcelImportController::class, 'showImportForm'])->name('import.form');
Route::post('/import', [ExcelImportController::class, 'import'])->name('import');
Route::get('/grafica', [ExcelImportController::class, 'grafica'])->name('grafica');
