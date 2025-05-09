<?php

namespace Modules\Excon\Filament\Resources;

use Carbon\Carbon;

use Modules\Excon\Filament\Resources\UnitResource\Pages;
use Modules\Excon\Filament\Resources\UnitResource\RelationManagers;
use Modules\Excon\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Modules\Excon\Models\Weapon;
use Modules\Excon\Filament\Resources\UnitResource\Widgets\UserTableWidget;
use Modules\Excon\Filament\Pages\UnitDashboard;

use Modules\Excon\Jobs\AntaresExportJob;


class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('side_id')
                    ->relationship(name: "side", titleAttribute: "name")
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\ColorColumn::make('side.data.color')
                    ->label('Side'),
                Tables\Columns\IconColumn::make('position_is_valid')
                    ->boolean(),
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
                Tables\Actions\Action::make('unit-dashboard')
                    ->label('Unit\'s dashboard')
                    ->url(fn($record) => UnitDashboard::getUrl(['unit' => $record])),
                Tables\Actions\Action::make('export-antares')
                    ->label('Export Antares data')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label("From")
                            ->native(false)
                            ->required(),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label("To")
                            ->native(false)
                            ->required(),
                    ])
                    ->action(function($record, $data){
                        AntaresExportJob::dispatch($record, Carbon::parse($data['start_date']), Carbon::parse($data['end_date']));
                    }),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make("antares-bulk-export")
                        ->label('Export Antares data')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\DateTimePicker::make('start_date')
                                ->label("From")
                                ->native(false)
                                ->required(),
                            Forms\Components\DateTimePicker::make('start_date')
                                ->label("To")
                                ->native(false)
                                ->required(),
                        ])
                        ->action(function($records, $data){
                            ddd($records, $data);
                        }),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            UserTableWidget::class,
        ];
    }
}
