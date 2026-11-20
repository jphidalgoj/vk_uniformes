<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (
            app()->isProduction() ||
            str_starts_with((string) config('app.url'), 'https://') ||
            request()->server('HTTP_X_FORWARDED_PROTO') === 'https' ||
            request()->server('HTTPS') === 'on'
        ) {
            URL::forceScheme('https');
        }

        Gate::define('access-admin', function (User $user) {
            return $user->is_admin;
        });
    }
}
