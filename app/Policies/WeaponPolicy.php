<?php

namespace Modules\Excon\Policies;

use App\Policies\GenericSkeletorPolicy;

class WeaponPolicy extends GenericSkeletorPolicy
{
    protected $slug = 'excon::weapons';
}