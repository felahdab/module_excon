<?php

namespace Modules\Excon\Filament\Pages\Widgets;

use Filament\Widgets\ChartWidget;

class WeaponsHistory extends ChartWidget
{
    protected static ?string $heading = 'Weapon history';

    public array $datasets = [];

    protected function getData(): array
    {
      return $this->datasets;
    }

    protected function getType(): string
    {
      return 'line';
    }
}
