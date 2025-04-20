<?php

namespace Modules\Excon\Filament\Pages\Widgets;

use Filament\Widgets\ChartWidget;

use Illuminate\Database\Eloquent\Model;

use Modules\Excon\Models\User;
use Modules\Excon\Models\Unit;
use Modules\Excon\Models\Weapon;
use Modules\Excon\Models\Engagement;


class WeaponsHistory extends ChartWidget
{
    protected static ?string $heading = 'Weapon history';

    public Unit $unit;
    private array $datasets = [];

    protected function getData(): array
    {
        /**
         * On implémente une mise en cache élémentaire pour la requête en cours.
         */
        if ($this->datasets)
        {
            return $this->datasets;
        }

        $datasets = [
              "datasets" =>  [
                [
                  "label" => "MM 40 Exocet",
                  "data" => [
                    0 => 0,
                    1 => 4,
                    2 => 3,
                    3 => 2,
                    4 => 2,
                  ]
                ]
              ],
            "labels" => [
              "Wed Apr 16 2025 00:00:00 GMT+0000",
              "Thu Apr 17 2025 00:00:00 GMT+0000",
              "Thu Apr 17 2025 21:42:14 GMT+0000",
              "Sun Apr 20 2025 21:39:56 GMT+0000",
              "Sun Apr 20 2025 22:13:48 GMT+0000",
            ]
        ];
        //ddd($this->datasets);
        //return $this->datasets;

        // TODO: fix this. The code below triggers a "Call to undefined method stdClass::getQueueableRelations()"
        $this->datasets =  $this->unit->weapons_history_for_widget;
        $this->unit->refresh();
        return $this->datasets;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
