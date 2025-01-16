<?php

namespace Modules\Excon\Filament\Resources;

use Modules\Excon\Filament\Resources\EngagementResource\Pages;
use Modules\Excon\Filament\Resources\EngagementResource\RelationManagers;
use Modules\Excon\Models\Engagement;
use Filament\Forms;
use Filament\Forms\Get;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;

use Modules\Excon\Models\Unit;


class EngagementResource extends Resource
{
    protected static ?string $model = Engagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('timestamp')
                    ->required()
                    ->default(now())
                    ->native(false),
                Forms\Components\Select::make('unit_id')
                    ->relationship(name: 'unit', titleAttribute: 'name')
                    ->required()
                    ->live(),
                Forms\Components\Select::make('weapon_id')
                    ->relationship(name: 'weapon', titleAttribute: 'name')
                    ->options(function (Get $get)
                        {
                            $unit = Unit::find($get("unit_id"));
                            if ($unit == null) return [];
                            $weapons = $unit->weapons;
                            return $weapons->pluck("name", "id");
                        })
                    ->required(),                
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('data.engagement_type')
                    ->options(["track_number" => "Track number", "absolute_position" => "Absolute position"])
                    ->live(),
                Forms\Components\TextInput::make('data.track_number')
                    ->visible(function (Get $get) {
                        return $get("data.engagement_type") == "track_number";
                    })
                    ->requiredIf('data.engagement_type', 'track_number'),
                Forms\Components\TextInput::make('data.target_latitude')
                    ->numeric()
                    ->visible(function (Get $get) {
                        return $get("data.engagement_type") == "absolute_position";
                    })
                    ->requiredIf('data.engagement_type', 'absolute_position'),
                Forms\Components\TextInput::make('data.target_longitude')
                    ->numeric()
                    ->visible(function (Get $get) {
                        return $get("data.engagement_type") == "absolute_position";
                    })
                    ->requiredIf('data.engagement_type', 'absolute_position')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('timestamp')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weapon.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label("Target")
                    ->formatStateUsing(function($record){
                        if (Arr::get($record->data, "engagement_type") == "track_number"){
                            return "TN: " . Arr::get($record->data, "track_number");
                        }
                        if (Arr::get($record->data, "engagement_type") == "absolute_position"){
                            $latitude = Arr::get($record->data, "target_latitude");
                            $longitude = Arr::get($record->data, "target_longitude");
                            
                            return "Position: " . $latitude . "/" . $longitude;
                        }
                            
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEngagements::route('/'),
            'create' => Pages\CreateEngagement::route('/create'),
            'edit' => Pages\EditEngagement::route('/{record}/edit'),
        ];
    }
}
