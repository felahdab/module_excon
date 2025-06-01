<?php

namespace Modules\Excon\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use Modules\Excon\Events\AffectUserToUnitEvent;
use Modules\Excon\Events\AffectUserToSideEvent;
use Modules\Excon\Events\AnAttackWasReportedEvent;
use Modules\Excon\Events\NirisTracksWhereRetreivedEvent;

use Modules\Excon\Listeners\AffectUserToUnitListener;
use Modules\Excon\Listeners\AffectUserToSideListener;
use Modules\Excon\Listeners\AnAttackWasReportedListener;
use Modules\Excon\Listeners\GenerateNvgFileWithNirisTracks;
use Modules\Excon\Listeners\UpdateDatabaseWithNirisTracks;


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
        ],
        AnAttackWasReportedEvent::class => [
            AnAttackWasReportedListener::class,
        ],
        NirisTracksWhereRetreivedEvent::class => [
            UpdateDatabaseWithNirisTracks::class,
            GenerateNvgFileWithNirisTracks::class
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
