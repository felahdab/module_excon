<?php

namespace Modules\Excon\Filament\Pages;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\MaxWidth;
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
use Modules\Excon\Models\Identifier;
use Modules\Excon\Models\EntityNumber;

class UnitDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'excon::filament.pages.my-unit-dashboard';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'unit-dashboard/{unit}';

    public ?array $data = [];
    public ?Unit $unit;

    public function mount(Unit $unit): void
    {
        $this->form->fill();
        $this->unit = $unit;
    }

    public static function canAccess(): bool
    {
        $unit = request('unit');

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
                ->modalWidth(MaxWidth::FiveExtraLarge)
                ->form([
                    Forms\Components\Section::make('Target course and speed')
                        ->columns(2)
                        ->schema([
                        Forms\Components\DateTimePicker::make('timestamp')
                            ->required()
                            ->default(now())
                            ->native(false),
                        Forms\Components\TextInput::make('assessed_target')
                            ->label("Assessed target")
                            ->required(),
                    ]),
                    Forms\Components\Section::make('Own position')
                        ->description('This section is displayed because the application doesn\'t have a valid position for your unit.')
                        ->columns(2)
                        ->hidden(function() use ($unit) {
                            return $unit->position_is_valid;
                        })
                        ->schema([
                            Forms\Components\TextInput::make('own_latitude')
                                ->label("Latitude"),
                            Forms\Components\TextInput::make('own_longitude')
                                ->label("Longitude"),

                    ]),
                    Forms\Components\Section::make('Weapon used')
                        ->columns(2)
                        ->schema([
                        Forms\Components\Select::make('weapon_id')
                            ->options(function () use ($unit)
                                {
                                    $ret = $unit->available_weapons;
                                    $unit->refresh();
                                    return $ret;
                                })
                            ->required(),                
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric(),
                    ]),
                    Forms\Components\Select::make('engagement_type')
                        ->options(["track_number" => "Track number", "absolute_position" => "Absolute position"])
                        ->live(),
                    Forms\Components\Section::make('Engagement on track number')
                        ->columns(2)
                        ->visible(function (Get $get) {
                            return $get("engagement_type") == "track_number";
                        })
                        ->schema([
                            Forms\Components\Select::make('track_number')
                                ->helperText('Only tracks with a valid position are shown. If your target doesn\'t appear, switch to absolute position and report the position of your targer.')
                                ->options(Identifier::source("LDT")->isValid()->get()->pluck('identifier', 'identifier'))
                                ->searchable()
                                ->requiredIf('engagement_type', 'track_number'),
                    ]),
                    Forms\Components\Section::make('Engagement on absolute position')
                        ->columns(2)
                        ->visible(function (Get $get) {
                            return $get("engagement_type") == "absolute_position";
                        })
                        ->schema([
                            Forms\Components\TextInput::make('target_latitude')
                                ->numeric()
                                ->requiredIf('engagement_type', 'absolute_position'),
                            Forms\Components\TextInput::make('target_longitude')
                                ->numeric()
                                ->requiredIf('engagement_type', 'absolute_position'),
                        ]),
                    Forms\Components\Section::make('Target course and speed')
                        ->description('Please specify the course and speed of your target as you would use in your weapon system. This will be used to assess the quality of the engagement.')
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('target_course')
                                ->numeric()
                                ->required(),
                            Forms\Components\TextInput::make('target_speed')
                                ->helperText('Your target speed in knots.')
                                ->numeric()
                                ->required(),
                        ])
                    ])
                ->visible(function() {
                    $ret = auth()->user()->can("excon::report_engagement_for_own_unit");
                    return $ret;
                })
                ->action(function ($data) use ($unit) {
                    if ($data["own_latitude"] && $data["own_longitude"])
                    {
                        $manual_identifier = $unit->identifiers()->source("MANUAL")->first();
                        if ($manual_identifier == null)
                        {
                            $manual_identifier = $unit->identifiers()->create([
                                "source" => "MANUAL",
                                "identifier" => "MANUAL",
                            ]);
                        }

                        $manual_identifier->positions()->create([
                            "latitude" => $data["own_latitude"],
                            "longitude" => $data["own_longitude"],
                            "timestamp" => $data["timestamp"],
                        ]);
                    }

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
                                        "target_course" => $data["target_course"] ?? 0,
                                        "target_speed" => $data["target_speed"] ? floatval($data["target_speed"])  * 1854 / 3600 : 0,
                                        "assessed_target" => $data["assessed_target"] ?? null,
                                    ]
                                ]);   
                    $unit->touch();         
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
                    ]),
                Section::make('Identifiers')
                    ->description('Unit identifiers')
                    ->columnSpan(1)
                    ->columns(2)
                    ->schema([
                        RepeatableEntry::make('identifiers')
                            ->label(false)
                            ->columnSpan(2)
                            ->columns(2)
                            ->schema([
                                TextEntry::make('source'),
                                TextEntry::make('identifier'),
                            ])
                    ]),
            ]);
        
        return $ret;
    }
}
