<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Hearing; 
use App\Observers\HearingObserver; 
use Illuminate\Support\Facades\Route;
use App\Observers\ActivityObserver;
use App\Models\Document;
use App\Models\User;

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
            Hearing::observe(HearingObserver::class);
            Document::observe(ActivityObserver::class);
            User::observe(ActivityObserver::class);

    }
}
