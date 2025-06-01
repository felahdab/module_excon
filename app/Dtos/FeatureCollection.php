<?php

namespace Modules\Excon\Dtos;

use Spatie\LaravelData\Data;

class FeatureCollection extends Data
{
    public string $type = "FeatureCollection";
    public array $features = [];

    public static function make($name)
    {
        return new static($name);
    }

    public function __construct(
        public string $name)
    {
        
    }

    public function addFeature(Feature $feature)
    {
        $this->features[] = $feature;
    }

    public function toGeoJson()
    {
        return json_encode($this->toArray());
    }

    public function toNVG()
    {
        $header = <<< EOT
<?xml version='1.0' encoding='UTF-8'?>
<nvg:nvg xmlns:nvg="http://tide.act.nato.int/schemas/2009/10/nvg" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:ns0="http://purl.org/dc/elements/1.1/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.w3.org/2001/XMLSchema http://www.w3.org/2001/XMLSchema.xsd " classification="NATO UNCLASSIFIED" version="1.5.0">

EOT; 
        $content ="";
        foreach($this->features as $feature)
        {
            $content = $content . $feature->toNVG();
        }
        $footer = <<< EOT
  
  <nvg:metadata>
    <dcterms:security.policy>NATO</dcterms:security.policy>
    <dcterms:security.classifcation>RESTRICTED</dcterms:security.classifcation>
  </nvg:metadata>
</nvg:nvg>
EOT; 

        return $header . $content . $footer;

    }
}