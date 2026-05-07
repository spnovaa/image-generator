<?php

namespace App\Providers;

use App\Events\ImageGenerated;
use App\Listeners\SendGeneratedImageNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

final class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ImageGenerated::class => [
            SendGeneratedImageNotification::class,
        ],
    ];
}
