<?php

namespace Modules\Excon\Filament\Resources\IdentifierResource\Pages;

use Modules\Excon\Filament\Resources\IdentifierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIdentifiers extends ListRecords
{
    protected static string $resource = IdentifierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
