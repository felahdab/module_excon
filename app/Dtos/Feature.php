<?php

namespace Modules\Excon\Dtos;

use Spatie\LaravelData\Data;

class Feature extends Data
{
    public string $type = "Feature";
    public array $properties = [];

    public function __construct(
        string $name, 
        string $symbol_name,
        public Geometry $geometry)
    {
        $this->properties = [
            "name" => $name,
            "symbol_name" => $symbol_name
        ];
    }

    public function toNVG()
    {
        return "<nvg:point course=\"0.0\" speed=\"0.0\" x=\"{$this->geometry->coordinates['longitude']}\" y=\"{$this->geometry->coordinates['latitude']}\" symbol=\"app6a:{$this->properties["symbol_name"]}\"  label=\"{$this->properties['name']}\"/>\n";
        //<nvg:point course="0.0" speed="0.0" x="-22.29505829113385" y="37.05190927917383" symbol="app6a:SUSPCLDD-------"/>
    }
}