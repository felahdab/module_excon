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

    private Unit $unit;
    private array $datasets = [];

    public function mount(): void
    {
        $excon_user = cast_as_eloquent_descendant(auth()->user(), User::class);
        $this->unit = $excon_user?->unit;
    }

    protected function getData(): array
    {
        $excon_user = cast_as_eloquent_descendant(auth()->user(), User::class);
        $this->unit = $excon_user?->unit;
        /**
         * On implémente une mise en cache élémentaire pour la requête en cours.
         */
        if ($this->datasets)
        {
            return $this->datasets;
        }

        // TODO: fix this. The code below triggers a "Call to undefined method stdClass::getQueueableRelations()"
        $this->datasets = $this->unit->weapons_history_for_widget;
        return $this->datasets;

        $this->datasets = 
        [
            'datasets' => [
              [
                "label" => "MM40",
                "data" => [ 0, 1, 2, 3, 4, 5, 6]
              ] ,
              [
                "label" => "Harpoon",
                "data" => [ 6, 5, 4, 3, 2, 1, 0]
              ] 
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        ];
        return $this->datasets;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
