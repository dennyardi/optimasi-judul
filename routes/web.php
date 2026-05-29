<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompetitorAnalysisController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SeoGenerationController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/generate', [SeoGenerationController::class, 'create'])->name('seo.create');
    Route::post('/generate', [SeoGenerationController::class, 'store'])->name('seo.store');

    Route::get('/competitors', [CompetitorAnalysisController::class, 'index'])->name('competitors.index');
    Route::post('/competitors', [CompetitorAnalysisController::class, 'store'])->name('competitors.store');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{generation}', [HistoryController::class, 'show'])->name('history.show');
    Route::delete('/history/{generation}', [HistoryController::class, 'destroy'])->name('history.destroy');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});
