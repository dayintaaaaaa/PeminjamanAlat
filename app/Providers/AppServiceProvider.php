<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;

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
        Event::listen(Login::class, fn (Login $e) => ActivityLog::record('Login', 'Auth', 'User logged in', $e->user));
        Event::listen(Logout::class, fn (Logout $e) => ActivityLog::record('Logout', 'Auth', 'User logged out', $e->user));
    }
}
