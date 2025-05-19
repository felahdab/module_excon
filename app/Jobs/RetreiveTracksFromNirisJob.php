<?php

namespace Modules\Excon\Jobs;

use Carbon\Carbon; 

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;

use Modules\Excon\Http\Integrations\Requests\RetreiveTracksRequest;
use Modules\Excon\Http\Integrations\Connectors\NIRISConnector;

use Modules\Excon\Models\Identifier;
use Modules\Excon\Models\Position;

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
        //logger()->info("Retreiving {$this->datasource} tracks.");
        $url = config("excon.niris_url");

        $server = new NIRISConnector($url);
        $request = new RetreiveTracksRequest($this->datasource);
        $response = $server->send($request);
        $response->throw();

        foreach($response->json()["tracks"] as $track)
        {
            /**
             * Coder ce qu'il faut faire de chaque piste reçue depuis le datastore NIRIS.
             * A prioro:
             * - creation d'un identifiant avec comme source TDL ou COT en fonction du datasource
             * - creation d'une position en base
             */
            //dump($track);
            $identifier = Arr::get($track, "domainSpecificTrackID", null);
            $latitude = Arr::get($track, "point.latitude", 0);
            $longitude = Arr::get($track, "point.longitude", 0);
            $course = Arr::get($track, "course", 0);
            $speed = Arr::get($track, "speed", 0);  // This is in m/s
            $timestamp = Arr::get($track, "observationDateTime", null); //: "2025-05-19T11:07:37.975Z",)
            if ($timestamp == null)
            {
                /**
                 * If there is not timestamp, we'll skip the track information.
                 */
                continue;
            }
            $carbon_time = Carbon::parse($timestamp)->clone()->addHours(1);

            $identifier = Identifier::firstOrCreate(
                [
                "source" => "LDT_{$this->datasource}",
                "identifier" => $identifier
                ]
            );

            $position = Position::where("identifier_id", $identifier->id)
                ->where("timestamp", $carbon_time)
                ->first();

            if ($position)
            {
                /** We already have a position for this timestamp for this identifier. We'll skip. */
                continue;
            }
            else {
                $position = Position::create(
                    [
                        "identifier_id" => $identifier->id,
                        "timestamp" => $carbon_time,
                        "latitude" => $latitude,
                        "longitude" => $longitude,
                        "course" => $course,
                        "speed" => $speed
                    ]
                    );
            }

        }
        
    }
}
