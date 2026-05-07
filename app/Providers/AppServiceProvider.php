<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Domain bindings live in DomainServiceProvider.
    }

    public function boot(): void
    {
        // Reserved for application bootstrap concerns (telemetry, gates, etc.).
    }
}
