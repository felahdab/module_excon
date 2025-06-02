<?php

namespace Modules\Excon\Services;

use Illuminate\Support\Facades\Storage;

use Modules\Excon\Dtos\FeatureCollection;
use Modules\Excon\Dtos\Feature;
use Modules\Excon\Dtos\Geometry;

class GenerateNVGFromTracksService
{
    public function __construct()
    {

    }

    public static function make()
    {
        return new static;
    }

    public function transform($name, $tracks, $filename)
    {
        logger()->info("Generate Nvg file from {$name} to {$filename}:");
        $geojson = FeatureCollection::make($name);
        foreach ($tracks as $track)
        {
            //dump($track);
            $geometry = new Geometry($track["point"]["latitude"], $track["point"]["longitude"]);
            $feature = new Feature($track["domainSpecificTrackID"], $track["symbolIdCode"], $geometry);
            $geojson->addFeature($feature);
        }
        //dump($geojson->toGeoJson());
        logger()->info(Storage::disk('public')->path("excon/nvgs/{$filename}"));
        Storage::disk('public')->put("excon/nvgs/{$filename}", $geojson->toNVG());
    }
    
}