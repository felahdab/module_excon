<?php

namespace Modules\Excon\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Modules\Excon\Events\AffectUserToSideEvent;

class AffectUserToSideListener
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
    public function handle(AffectUserToSideEvent $event): void
    {
        if ($event->side == null)
        {
            $event->user->sides()->sync([]);
            return;
        }
        $event->user->sides()->sync([$event->side->id]);
    }
}
