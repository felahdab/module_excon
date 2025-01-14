<?php

namespace Modules\Excon\Filament\Resources\EngagementResource\Pages;

use Modules\Excon\Filament\Resources\EngagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEngagements extends ListRecords
{
    protected static string $resource = EngagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
