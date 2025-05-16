<?php

namespace Modules\Excon\Http\Integrations\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class RetreiveTracksRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public string $datasource) {
        $this->query()->add('dataSources', $datasource);
    }

    public function resolveEndpoint(): string
    {
        return "/rest/tracks";
    }
}