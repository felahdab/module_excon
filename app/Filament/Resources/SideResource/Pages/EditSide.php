<?php

namespace Modules\Excon\Filament\Resources\SideResource\Pages;

use Modules\Excon\Filament\Resources\SideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Modules\Excon\Filament\Resources\SideResource\Widgets\UserTableWidget   ;


class EditSide extends EditRecord
{
    protected static string $resource = SideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return[
            UserTableWidget::class,
        ];
    }
}
