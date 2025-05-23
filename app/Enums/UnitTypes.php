<?php

namespace Modules\Excon\Enums;

use Filament\Support\Contracts\HasLabel;

enum UnitTypes : string implements HasLabel
{
    case SURFACE_SHIP = 'surface ship';
    case SUBMARINE = 'submarine';
    case STAFF = 'staff';

    public function getLabel(): ?string{
        return $this->name;
    }
}