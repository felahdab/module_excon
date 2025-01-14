<?php

namespace Modules\Excon\Filament\Resources\WeaponResource\Pages;

use Modules\Excon\Filament\Resources\WeaponResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeapons extends ListRecords
{
    protected static string $resource = WeaponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
