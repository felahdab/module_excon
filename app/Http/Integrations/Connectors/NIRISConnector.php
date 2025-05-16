<?php

namespace Modules\Excon\Http\Integrations\Connectors;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Http\Auth\TokenAuthenticator;

class NIRISConnector extends Connector
{
    use AcceptsJson;

    public function __construct(public readonly string $url) 
    {

    }

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        // Typiquement https://1.2.3.4:2001/service/nwac/rest
        return $this->url;
    }

    /**
     * Default headers for every request
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Default HTTP client options
     */
    protected function defaultConfig(): array
    {
        return [
            'verify' => false,
        ];
    }
}
