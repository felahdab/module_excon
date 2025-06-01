<?php

namespace Modules\Excon\Listeners;

use Carbon\Carbon;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;

use Modules\Excon\Events\NirisTracksWhereRetreivedEvent;
use Modules\Excon\Models\Identifier;
use Modules\Excon\Models\Position;


class UpdateDatabaseWithNirisTracks
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
    public function handle(NirisTracksWhereRetreivedEvent $event): void
    {
        //logger()->info("Updating from {$event->datasource}");
        foreach($event->tracks as $track)
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
            $carbon_time = Carbon::parse($timestamp);

            $identifier = Identifier::firstOrCreate(
                [
                "source" => "LDT_{$event->datasource}",
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
                $identifier->setLatestPositionReceived($position);
            }

        }
    }
}
