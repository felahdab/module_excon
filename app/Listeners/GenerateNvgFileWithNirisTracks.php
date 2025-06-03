<?php

namespace Modules\Excon\Listeners;

use Carbon\Carbon;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;

use Modules\Excon\Events\NirisTracksWhereRetreivedEvent;
use Modules\Excon\Models\Identifier;
use Modules\Excon\Models\Position;

use Modules\Excon\Services\GenerateNVGFromTracksService;


class GenerateNvgFileWithNirisTracks
{
    public string $filename;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     */
    public function handle(NirisTracksWhereRetreivedEvent $event): void
    {
        //logger()->info("Generating Nvg file from {$event->datasource} dated {$event->timestamp}.");
        $this->filename = "{$event->timestamp->format('YmdHi')}_{$event->datasource}_sitac.nvg"; 
        GenerateNVGFromTracksService::make()->transform($event->datasource, $event->tracks, $this->filename);
    }
}
