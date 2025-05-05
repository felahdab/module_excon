<?php

namespace Modules\Excon\Enums;

enum WeaponTypes : string
{
    case SURFACE_TO_SURFACE = 'surface to surface';
    case SURFACE_TO_AIR = 'surface to air';
    case ARTILLERY = 'artillery';
    case AIR_TO_SURFACE = 'air to surface';
    case AIR_TO_AIR = 'air to air';
    case SURFACE_TO_SUBSURFACE = 'surface to subsurface';
    case AIR_TO_SUBSURFACE = 'air to subsurface';
}