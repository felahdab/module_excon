<?php

namespace Modules\Excon\Filament\Pages;

use Modules\Excon\Models\User;
use Modules\Excon\Models\Unit;
use Modules\Excon\Enums\UnitTypes;


class MyUnitDashboard extends UnitDashboard
{
    protected static ?string $slug = 'my-unit-dashboard';

    protected static bool $shouldRegisterNavigation = true;

    public ?User $excon_user;

    public function mount(Unit $unit): void
    {
        $this->form->fill();
        $this->excon_user = cast_as_eloquent_descendant(auth()->user(), User::class);
        $this->unit = $this->excon_user?->unit;
    }

    public static function canAccess(): bool
    {
        $result = auth()->check() && 
                    cast_as_eloquent_descendant(auth()->user(), User::class)?->unit != null &&
                    cast_as_eloquent_descendant(auth()->user(), User::class)?->unit->type != UnitTypes::STAFF->value;

        return $result;
    }

}
