<?php

namespace Modules\Excon\Filament\Resources\EngagementResource\Pages;

use Modules\Excon\Filament\Resources\EngagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEngagement extends EditRecord
{
    protected static string $resource = EngagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
