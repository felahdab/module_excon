<?php

namespace Modules\Excon\Services;

use Illuminate\Support\Facades\Storage;

use Modules\Excon\Dtos\FeatureCollection;
use Modules\Excon\Dtos\Feature;
use Modules\Excon\Dtos\Geometry;

class GenerateGeoJsonFromTracksService
{
    public function __construct()
    {

    }

    public static function make()
    {
        return new static;
    }

    public function transform($datasource, $filename)
    {
        $tracks = RetreiveTracksFromNirisService::make()->getTracks($datasource);

        $geojson = FeatureCollection::make($datasource);
        foreach ($tracks as $track)
        {
            //dump($track);
            $geometry = new Geometry($track["point"]["latitude"], $track["point"]["latitude"]);
            $feature = new Feature($track["domainSpecificTrackID"], $track["symbolIdCode"], $geometry);
            $geojson->addFeature($feature);
        }
        //dump($geojson->toGeoJson());

        Storage::disk('public')->put("excon/geojson/{$filename}", $geojson->toGeoJson());
    }
    
}