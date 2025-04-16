<?php

namespace Modules\Excon\Filament\Pages;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Livewire;

use Modules\Excon\Filament\Pages\Widgets\WeaponsHistory;


use Modules\Excon\Models\User;
use Modules\Excon\Models\Unit;
use Modules\Excon\Models\Weapon;
use Modules\Excon\Models\Engagement;

class MyUnitDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'excon::filament.pages.my-unit-dashboard';

    protected static ?string $slug = 'my-unit-dashboard';

    public ?array $data = [];
    public ?User $excon_user;
    public ?Unit $unit;

    public function mount(): void
    {
        $this->form->fill();
        $this->excon_user = cast_as_eloquent_descendant(auth()->user(), User::class);
        $this->unit = $this->excon_user?->unit;
    }

    public static function canAccess(): bool
    {
        return auth()->check() && 
            cast_as_eloquent_descendant(auth()->user(), User::class)->unit != null;
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data');
    }

    public function weaponsInfolist(Infolist $infolist): Infolist
    {
        $ammo_load = $this->unit->ammunition_load;
        $schema = [];
        foreach($ammo_load as $weaponid => $amount){
            $weapon = Weapon::find($weaponid);
            $schema[]= (object) [
                "name" => $weapon->name,
                "amount" => $amount
            ];
        }
        $this->unit->weapon_loads = $schema;

        $engagements = Engagement::where('unit_id', $this->unit->id)
            ->orderBy('timestamp', 'desc')
            ->get();
        $engs = [];
        foreach($engagements as $engagement)
        {
            $engs[] = (object) [
                "timestamp" => $engagement->timestamp,
                "weapon" => $engagement->weapon->name,
                "amount" => $engagement->amount,
                "target" => $engagement->target
            ];
        }
        $this->unit->engagements = $engs;
        //ddd($this->unit);

        return $infolist
            ->record($this->unit)
            ->columns(2)
            ->schema([
                Section::make('Unit')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('name'),
                    ]),
                Livewire::make(WeaponsHistory::class, ["unit" => $this->unit]),
                Section::make('Weapons')
                    ->description('Weapons load')
                    ->columnSpan(1)
                    ->columns(2)
                    ->schema([
                        RepeatableEntry::make('weapon_loads')
                        ->columnSpan(2)
                        ->columns(2)
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('amount'),
                        ])
                    ]),
                Section::make('Engagements')
                    ->description('History of shots')
                    ->columnSpan(1)
                    ->columns(4)
                    ->schema([
                        RepeatableEntry::make('engagements')
                        ->columnSpan(4)
                        ->columns(4
                        )
                        ->schema([
                            TextEntry::make('timestamp'),
                            TextEntry::make('weapon'),
                            TextEntry::make('amount'),
                            TextEntry::make('target'),
                        ])
                    ])
            ]);
    }
}
