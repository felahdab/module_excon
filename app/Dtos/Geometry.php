<?php

namespace Modules\Excon\Dtos;

use Spatie\LaravelData\Data;

class Geometry extends Data
{
    public string $type = "Point";
    public array $coordinates = [];

    public function __construct(
        float $latitude, 
        float $longitude)
    {
        $this->coordinates["latitude"] = $latitude;
        $this->coordinates["longitude"] = $longitude;
    }
}