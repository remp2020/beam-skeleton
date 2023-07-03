<?php

namespace App\Providers;

use App\Observers\AccountObserver;
use Illuminate\Support\ServiceProvider;
use Remp\BeamModule\Model\Account;

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
        Account::observe(AccountObserver::class);
    }
}
