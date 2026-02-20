<?php

use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\SimpleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [SimpleAuthController::class, 'showLoginForm'])->name('simple.login');
Route::post('/login', [SimpleAuthController::class, 'login'])->name('simple.login.post');
Route::post('/logout', [SimpleAuthController::class, 'logout'])->name('simple.logout');

Route::middleware('simple.auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/import', [ExcelImportController::class, 'showImportForm'])->name('import.form');
    Route::post('/import', [ExcelImportController::class, 'import'])->name('import');
    Route::get('/grafica', [ExcelImportController::class, 'grafica'])->name('grafica');
});
