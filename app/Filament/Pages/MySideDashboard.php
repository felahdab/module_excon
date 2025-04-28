<?php

namespace Modules\Excon\Filament\Pages;

use Modules\Excon\Models\User;


class MySideDashboard extends SideDashboard
{
    protected static ?string $slug = 'my-side-dashboard';

    protected static bool $shouldRegisterNavigation = true;

    public ?User $excon_user;

    public function mount(?int $sideid=null): void
    {
        $this->form->fill();
        $this->excon_user = cast_as_eloquent_descendant(auth()->user(), User::class);
        $this->side = $this->excon_user?->side;
    }

    public static function canAccess(): bool
    {
        $result = auth()->check() && 
                    cast_as_eloquent_descendant(auth()->user(), User::class)?->side != null;

        return $result;
    }

}
