<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\TradeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TradeScreenshotController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\RulesTipsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AIInsightsController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AccountController;

// Public routes
Route::get('/login', function () {
    return redirect()->route('dashboard.index');
})->name('login');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // Breeze compatibility
    Route::get('/analytics', [DashboardController::class, 'index'])->name('analytics.index');

    // Trades
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

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::get('/trades/date/{date}', [CalendarController::class, 'getTradesByDate'])->name('trades.by_date');

    // Reminders
    Route::get('/reminders/upcoming', [ReminderController::class, 'upcoming'])->name('reminders.upcoming');
    Route::post('/reminders/{reminder}/notified', [ReminderController::class, 'notified'])->name('reminders.notified');
    Route::resource('reminders', ReminderController::class)->only(['index', 'store', 'update', 'destroy']);

    // Rules & Tips
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

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/api-key', [ProfileController::class, 'updateApiKey'])->name('profile.update-api-key');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AI Insights
    Route::get('/ai-insights', [AIInsightsController::class, 'index'])->name('ai-insights.index');
    Route::post('/ai-insights/analyze', [AIInsightsController::class, 'analyze'])->name('ai-insights.analyze');
    Route::get('/ai-insights/history', [AIInsightsController::class, 'history'])->name('ai-insights.history');
    Route::post('/ai-insights/monthly', [AIInsightsController::class, 'monthlyInsights'])->name('ai-insights.monthly');

    // Milestones
    Route::get('/milestones', [App\Http\Controllers\MilestoneController::class, 'index'])->name('milestones.index');
    Route::post('/milestones', [App\Http\Controllers\MilestoneController::class, 'store'])->name('milestones.store');
    Route::delete('/milestones/{milestone}', [App\Http\Controllers\MilestoneController::class, 'destroy'])->name('milestones.destroy');

    // Assets
    Route::resource('assets', AssetController::class)->except(['create', 'show', 'edit']);

    // Accounts
    Route::post('/accounts/{account}/archive', [AccountController::class, 'archive'])->name('accounts.archive');
    Route::resource('accounts', AccountController::class);

    // Single Trade View
    Route::get('/trades/{trade}', [TradeController::class, 'show'])->name('trades.show');
});

require __DIR__ . '/auth.php';
