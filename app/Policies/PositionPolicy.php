<?php

namespace Modules\Excon\Policies;

use App\Policies\GenericSkeletorPolicy;

class PositionPolicy extends GenericSkeletorPolicy
{
    protected $slug = 'excon::positions';
}