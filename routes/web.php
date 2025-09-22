<?php

use App\Http\Controllers\TradeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TradeScreenshotController;
use App\Http\Controllers\ReminderController;


Route::get('/', [TradeController::class, 'index'])->name('dashboard.index');
Route::get('/trades/data', [TradeController::class, 'getTrades'])->name('trades.data');
Route::post('/trades', [TradeController::class, 'store'])->name('trades.store');
Route::delete('/trades/{trade}', [TradeController::class, 'destroy'])->name('trades.destroy');
Route::put('/trades/{trade}', [TradeController::class, 'update'])->name('trades.update');
Route::get('/trades/{trade}/edit', [TradeController::class, 'edit'])->name('trades.edit');


//i want an array of all tuesdays since 2019-08-06 till 2025-09-15
Route::get('all', function () {
    $start = \Carbon\Carbon::parse('2019-08-06');
    $end = \Carbon\Carbon::parse('2025-09-15');
    $tuesdays = [];

    // Set to first Tuesday on/after start date
    if ($start->dayOfWeek !== \Carbon\Carbon::TUESDAY) {
        $start->next(\Carbon\Carbon::TUESDAY);
    }

    while ($start->lte($end)) {
        $tuesdays[] = $start->format('Y-m-d');
        $start->addWeek();
    }

    return $tuesdays;
});



Route::resource('reminders', ReminderController::class)->only(['index', 'store', 'destroy']);


Route::prefix('trades/{trade}/screenshots')->group(function () {
    Route::post('/', [TradeScreenshotController::class, 'store'])->name('screenshots.store');
});

Route::delete('/screenshots/{screenshot}', [TradeScreenshotController::class, 'destroy'])->name('screenshots.destroy');
