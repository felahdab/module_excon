<?php

namespace Modules\Excon\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Modules\Excon\Http\Integrations\Requests\RetreiveTracksRequest;
use Modules\Excon\Http\Integrations\Connectors\NIRISConnector;

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
        $url = config("excon.niris_url");
        $token=config("excon.niris_token");

        $server = new NIRISConnector($url, $token);
        $request = new RetreiveTracksRequest($this->datasource);
        $response = $server->send($request);
        $response->throw();

        foreach($response->json() as $track)
        {
            /**
             * Coder ce qu'il faut faire de chaque piste reçue depuis le datastore NIRIS.
             * A prioro:
             * - creation d'un identifiant avec comme source TDL ou COT en fonction du datasource
             * - creation d'une position en base
             */
            dump($track);
        }
        
    }
}
