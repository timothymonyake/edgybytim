<?php

use Illuminate\Support\Facades\Route;
use Modules\Deriv\Http\Controllers\DerivDashboardController;

Route::middleware(['web', 'auth'])->prefix('deriv')->name('deriv.')->group(function () {
    Route::get('/dashboard', [DerivDashboardController::class, 'index'])->name('dashboard');
    Route::post('/accounts', [DerivDashboardController::class, 'store'])->name('accounts.store');
    Route::get('/api/equity/{account}', [DerivDashboardController::class, 'getEquityData'])->name('api.equity');
});
