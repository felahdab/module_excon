<?php

namespace Modules\Excon\Http\Integrations\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class RetreiveKmlFileRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public string $datasource) {
        
    }

    public function resolveEndpoint(): string
    {
        return "/tracks/{$this->datasource}/";
    }
}