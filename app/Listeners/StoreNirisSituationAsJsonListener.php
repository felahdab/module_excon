<?php

namespace Modules\Excon\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Modules\Excon\Events\NirisSituationWasRetreivedEvent;

use Illuminate\Support\Facades\Storage;

class StoreNirisSituationAsJsonListener
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
    public function handle(NirisSituationWasRetreivedEvent $event): void
    {
        $situation = $event->situation;
        $filename = "{$event->timestamp->format('YmdHi')}_{$event->datasource}_sitac.json"; 
        //logger()->info(json_encode($situation));
        Storage::disk('public')->put("excon/json/{$filename}", json_encode($situation));
    }
}
