<?php

namespace Modules\Excon\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Modules\Excon\Events\AffectUserToUnitEvent;

class AffectUserToUnitListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AffectUserToUnitEvent $event): void
    {
        $event->user->units()->sync([$event->unit->id]);
    }
}
