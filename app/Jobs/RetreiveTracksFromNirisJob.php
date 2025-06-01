<?php

namespace Modules\Excon\Jobs;

use Carbon\Carbon; 

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Modules\Excon\Services\RetreiveTracksFromNirisService;



use Modules\Excon\Events\NirisTracksWhereRetreivedEvent;

class RetreiveTracksFromNirisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $datasource)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tracks = RetreiveTracksFromNirisService::make()->getTracks($this->datasource);
        event(new NirisTracksWhereRetreivedEvent($this->datasource, $tracks));
    }
}
