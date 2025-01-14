<?php

namespace Modules\Excon\Filament\Resources\SideResource\Pages;

use Modules\Excon\Filament\Resources\SideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSide extends EditRecord
{
    protected static string $resource = SideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
