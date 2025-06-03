<?php
namespace Modules\Excon\Services;

use Modules\Excon\Http\Integrations\Requests\RetreiveTracksRequest;
use Modules\Excon\Http\Integrations\Connectors\NIRISConnector;

use Modules\Excon\Events\NirisSituationWasRetreivedEvent;

class RetreiveTracksFromNirisService {
    public static function make()
    {
        return new static;
    }
    
    public function getTracks($datasource)
    {
                //logger()->info("Retreiving {$this->datasource} tracks.");
                $url = config("excon.niris_url");

                $server = new NIRISConnector($url);
                $request = new RetreiveTracksRequest($datasource);
                $response = $server->send($request);
                $response->throw();

                $e = new NirisSituationWasRetreivedEvent($datasource, $response->json());
                event($e);

                return $response->json()["tracks"];
    }

}