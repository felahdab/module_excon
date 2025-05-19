<?php

namespace Modules\Excon\Http\Integrations\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class RetreiveKmlFileRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public string $datasource) {
        $this->query()->add('trackStyle', "STREET_MAP");
        //GOOGLE_EARTH
        //NATO_MAJIIC
        //STREET_MAP
    }

    public function resolveEndpoint(): string
    {
        return "rest/kml/{$this->datasource}.kml";
    }
}