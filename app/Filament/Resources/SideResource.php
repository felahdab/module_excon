<?php

namespace Modules\Excon\Filament\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Modules\Excon\Filament\Pages\SideDashboard;
use Modules\Excon\Filament\Resources\SideResource\Pages;
use Modules\Excon\Filament\Resources\SideResource\RelationManagers;
use Modules\Excon\Models\Side;

class SideResource extends Resource
{
    protected static ?string $model = Side::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\ColorPicker::make('data.color')
                    ->label("Color"),
                Forms\Components\Repeater::make('data.sources')
                    ->label("Position sources associated to this side")
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ColorColumn::make('data.color'),
                    
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
                Tables\Actions\Action::make('side-dashboard')
                    ->label("Side dashboard")
                    ->url(fn($record) => SideDashboard::getUrl(['side' => $record])),
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
            'index' => Pages\ListSides::route('/'),
            'create' => Pages\CreateSide::route('/create'),
            'edit' => Pages\EditSide::route('/{record}/edit'),
        ];
    }
}
