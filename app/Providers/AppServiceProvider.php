<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\StudentActivity;
use Illuminate\Support\Facades\View;

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
        $this->app->booted(function () {
            \Illuminate\Support\Facades\View::composer('*', function ($view) {
                try {
                    $activities = \App\Models\StudentActivity::latest()->take(10)->get();
                } catch (\Throwable $e) {
                    $activities = collect(); // fallback empty
                }
                $view->with('activities', $activities);
            });
        });
    }
}
