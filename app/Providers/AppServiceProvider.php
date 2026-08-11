<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Trade::observe(\App\Observers\TradeObserver::class);
        \App\Models\Milestone::observe(\App\Observers\MilestoneObserver::class);

        // Register Deriv Module Routes
        if (file_exists(base_path('Modules/Deriv/routes.php'))) {
            $this->loadRoutesFrom(base_path('Modules/Deriv/routes.php'));
        }

        // Register Deriv Module Views
        if (is_dir(base_path('Modules/Deriv/Resources/views'))) {
            $this->loadViewsFrom(base_path('Modules/Deriv/Resources/views'), 'deriv');
        }

        // Register Deriv Module Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Modules\Deriv\Console\Commands\DerivConnectCommand::class,
                \Modules\Deriv\Console\Commands\DerivSyncCommand::class,
                \Modules\Deriv\Console\Commands\DerivMockCommand::class,
            ]);
        }
    }
}
