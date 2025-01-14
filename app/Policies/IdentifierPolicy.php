<?php

namespace Modules\Excon\Policies;

use App\Policies\GenericSkeletorPolicy;

class IdentifierPolicy extends GenericSkeletorPolicy
{
    protected $slug = 'excon::identifiers';
}