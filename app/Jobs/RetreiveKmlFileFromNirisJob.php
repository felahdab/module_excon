<?php

namespace Modules\Excon\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

use Modules\Excon\Http\Integrations\Requests\RetreiveKmlFileRequest;
use Modules\Excon\Http\Integrations\Connectors\NIRISConnector;

class RetreiveKmlFileFromNirisJob implements ShouldQueue
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
        $url = config("excon.niris_url");

        $server = new NIRISConnector($url);
        $request = new RetreiveKmlFileRequest($this->datasource);
        $response = $server->send($request);
        $response->throw();

        $timestamp = now()->format('Y-m-d_H-i-s');

        Storage::disk('public')->put("excon/kml/{$this->datasource}_{$timestamp}.kml", $response->body());
        dump(Storage::disk('public')->url("excon/kml/{$this->datasource}_{$timestamp}.kml"));
         
    }
}
