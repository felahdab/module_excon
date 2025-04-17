<?php

namespace Modules\Excon\Filament\Resources\UnitResource\Pages;

use Modules\Excon\Filament\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Get;

use Modules\Excon\Models\Weapon;
use Modules\Excon\Models\Unit;
use Modules\Excon\Models\EntityNumber;

use Modules\Excon\Filament\Resources\UnitResource\Widgets\UserTableWidget;


class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make("load_weapon")
                ->requiresConfirmation()
                ->form([
                    
                    Select::make("weapon_type")
                        ->options(Weapon::all()->pluck('name', 'id'))
                        ->required(),
                    TextInput::make("amount")
                        ->numeric()
                        ->default(1)
                        ->required(),
                    DateTimePicker::make("timestamp")
                        ->default(now())
                        ->required()
                ])
                ->action(function ($data, $record){
                    $weapon=Weapon::find($data["weapon_type"]);
                    $record->weapons()
                        ->attach($weapon, ["amount" => $data["amount"],
                                        "timestamp" => $data["timestamp"]
                                        ]);            
                }),
            Actions\Action::make("record_engagement")
                ->requiresConfirmation()
                ->form([
                    Forms\Components\DateTimePicker::make('timestamp')
                        ->required()
                        ->default(now())
                        ->native(false),
                    Forms\Components\Select::make('weapon_id')
                        ->options(function ()
                            {
                                $unit = $this->getRecord();
                                return $unit->available_weapons;
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
                ->action(function ($data, $record){
                    $weapon = Weapon::find($data["weapon_id"]);
                    $stock_before_engagement = $record->available_weapons[$weapon->id] ?? 0;

                    if ($data["amount"] > $stock_before_engagement)
                    {
                        return;
                    }
                    $record->engagements()
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

    protected function getFooterWidgets(): array
    {
        return[
            UserTableWidget::class,
        ];
    }
}
