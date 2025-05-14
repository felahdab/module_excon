<?php

namespace Modules\Excon\Filament\Resources\UnitResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Modules\Excon\Filament\Resources\UnitResource;
use Modules\Excon\Filament\Pages\UnitDashboard;
use Modules\Excon\Filament\Resources\UnitResource\Widgets\UserTableWidget;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
                Actions\Action::make("dashboard")
                    ->label('Unit dashboard')
                    ->url(fn($record) => UnitDashboard::getUrl(['unit' => $record]))
        ];
    }

    protected function getFooterWidgets(): array
    {
        return[
            UserTableWidget::class,
        ];
    }
}
