<?php

namespace Modules\Excon\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use Modules\Excon\Events\AffectUserToUnitEvent;
use Modules\Excon\Events\AffectUserToSideEvent;

use Modules\Excon\Listeners\AffectUserToUnitListener;
use Modules\Excon\Listeners\AffectUserToSideListener;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        AffectUserToUnitEvent::class => [
            AffectUserToUnitListener::class,
        ],
        AffectUserToSideEvent::class => [
            AffectUserToSideListener::class,
        ]
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
        //
    }
}
