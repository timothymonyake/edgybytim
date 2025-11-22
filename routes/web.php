<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\TradeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TradeScreenshotController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\RulesTipsController;
use App\Http\Controllers\DashboardController;


Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/analytics', [DashboardController::class, 'index'])->name('analytics.index');
Route::get('/trades', [TradeController::class, 'index'])->name('trades.index');
Route::get('/trades/data', [TradeController::class, 'getTrades'])->name('trades.data');
Route::post('/trades', [TradeController::class, 'store'])->name('trades.store');
Route::delete('/trades/{trade}', [TradeController::class, 'destroy'])->name('trades.destroy');
Route::put('/trades/{trade}', [TradeController::class, 'update'])->name('trades.update');
Route::get('/trades/{trade}/edit', [TradeController::class, 'edit'])->name('trades.edit');
Route::post('/trades/{trade}/screenshots', [TradeScreenshotController::class, 'store'])->name('screenshots.store');
Route::delete('/screenshots/{screenshot}', [TradeScreenshotController::class, 'destroy'])->name('screenshots.destroy');
Route::get('/screenshots/{screenshot}/edit', [TradeScreenshotController::class, 'edit'])
    ->name('trades.screenshots.edit');
Route::put('/screenshots/{screenshot}', [TradeScreenshotController::class, 'update'])
    ->name('trades.screenshots.update');


Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
Route::get('/trades/date/{date}', [CalendarController::class, 'getTradesByDate'])->name('trades.by_date');



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

Route::get('/rules-tips', [RulesTipsController::class, 'index'])->name('rules_tips.index');
Route::post('/rules', [RulesTipsController::class, 'storeRule'])->name('rules.store');
Route::put('/rules/{rule}', [RulesTipsController::class, 'updateRule'])->name('rules.update');
Route::delete('/rules/{rule}', [RulesTipsController::class, 'destroyRule'])->name('rules.destroy');

Route::post('/tips', [RulesTipsController::class, 'storeTip'])->name('tips.store');
Route::put('/tips/{tip}', [RulesTipsController::class, 'updateTip'])->name('tips.update');
Route::delete('/tips/{tip}', [RulesTipsController::class, 'destroyTip'])->name('tips.destroy');

Route::post('/checklists', [RulesTipsController::class, 'storeChecklist'])->name('checklists.store');
Route::put('/checklists/{checklist}', [RulesTipsController::class, 'updateChecklist'])->name('checklists.update');
Route::delete('/checklists/{checklist}', [RulesTipsController::class, 'destroyChecklist'])->name('checklists.destroy');

