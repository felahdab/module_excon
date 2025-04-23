<?php

namespace Modules\Excon\Filament\Pages;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Livewire;

use Modules\Excon\Filament\Pages\Widgets\WeaponsHistory;


use Modules\Excon\Models\User;
use Modules\Excon\Models\Unit;
use Modules\Excon\Models\Weapon;
use Modules\Excon\Models\Engagement;
use Modules\Excon\Models\EntityNumber;

class UnitDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'excon::filament.pages.my-unit-dashboard';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'unit-dashboard/{unitid}';

    public ?array $data = [];
    public ?Unit $unit;

    public function mount(?int $unitid = null): void
    {
        $this->form->fill();
        $this->unit = Unit::findOrFail($unitid);
    }

    public static function canAccess(): bool
    {
        $unitid = intval(request('unitid'));
        $unit = Unit::findOrFail($unitid);

        $result = auth()->check() && ( 
                                        cast_as_eloquent_descendant(auth()->user(), User::class)->unit?->id == $unit->id
                                        || auth()->user()->can("excon::view_all_units_dashboard") 
                                    );

                    
        return $result;
    }

    protected function getHeaderActions(): array
    {
        $unit = $this->unit;

        return [
            Actions\Action::make("report_engagement")
                ->requiresConfirmation()
                ->form([
                    Forms\Components\DateTimePicker::make('timestamp')
                        ->required()
                        ->default(now())
                        ->native(false),
                    Forms\Components\Select::make('weapon_id')
                        ->options(function () use ($unit)
                            {
                                //return [];
                                //$unit = $this->getRecord();
                                $ret = $unit->available_weapons;
                                $unit->refresh();
                                return $ret;
                            })
                        ->required(),                
                    Forms\Components\TextInput::make('amount')
                        ->required()
                        ->numeric(),
                    Forms\Components\Select::make('engagement_type')
                        ->options(["track_number" => "Track number", "absolute_position" => "Absolute position"])
                        ->live(),
                    Forms\Components\TextInput::make('track_number')
                        ->visible(function (Get $get) {
                            return $get("engagement_type") == "track_number";
                        })
                        ->requiredIf('engagement_type', 'track_number'),
                    Forms\Components\TextInput::make('target_latitude')
                        ->numeric()
                        ->visible(function (Get $get) {
                            return $get("engagement_type") == "absolute_position";
                        })
                        ->requiredIf('engagement_type', 'absolute_position'),
                    Forms\Components\TextInput::make('target_longitude')
                        ->numeric()
                        ->visible(function (Get $get) {
                            return $get("engagement_type") == "absolute_position";
                        })
                        ->requiredIf('engagement_type', 'absolute_position'),
                ])
                ->action(function ($data) use ($unit) {
                    $weapon = Weapon::find($data["weapon_id"]);
                    $stock_before_engagement = $unit->available_weapons[$weapon->id] ?? 0;

                    if ($data["amount"] > $stock_before_engagement)
                    {
                        return;
                    }
                    $unit->engagements()
                        ->create([  "weapon_id" => $weapon->id,
                                    "amount"    => $data["amount"],
                                    "timestamp" => $data["timestamp"],
                                    "entity_number" => EntityNumber::getNewEntityNumber(),
                                    "data" => [
                                        "engagement_type" => $data["engagement_type"],
                                        "track_number" => $data["track_number"] ?? null,
                                        "target_latitude" => $data["target_latitude"] ?? null,
                                        "target_longitude" => $data["target_longitude"] ?? null,
                                    ]
                                ]);            
                }),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data');
    }

    public function weaponsInfolist(Infolist $infolist): Infolist
    {
        $historical_datasets =  $this->unit->weapons_history_for_widget;

        $ret = $infolist
            ->record($this->unit)
            ->columns(2)
            ->schema([
                Section::make('Unit')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('name')
                            ->columnSpan(1),
                        IconEntry::make('position_is_valid')
                            ->columnSpan(1)
                            ->boolean()
                            ->color(fn ($record): string => match ($record->position_is_valid) {
                                true => 'success',
                                false => 'danger',
                            }),
                    ]),
                Livewire::make(WeaponsHistory::class, ["datasets" => $historical_datasets]),
                Section::make('Weapons')
                    ->description('Current ammunition available')
                    ->columnSpan(1)
                    ->columns(2)
                    ->schema([
                        RepeatableEntry::make('weapons_loads')
                        ->label(false)
                        ->columnSpan(2)
                        ->columns(2)
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('amount')
                                ->label("Amount currently available"),
                        ])
                    ]),
                Section::make('Engagements')
                    ->description('History of shots')
                    ->columnSpan(1)
                    ->columns(4)
                    ->schema([
                        RepeatableEntry::make('engagements_history')
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
        
        return $ret;
    }
}
