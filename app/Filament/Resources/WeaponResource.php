<?php

namespace Modules\Excon\Filament\Resources;

use Modules\Excon\Filament\Resources\WeaponResource\Pages;
use Modules\Excon\Filament\Resources\WeaponResource\RelationManagers;
use Modules\Excon\Models\Weapon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WeaponResource extends Resource
{
    protected static ?string $model = Weapon::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('speed')
                    ->helperText("Speed in m/s")
                    ->required()
                    ->numeric()
                    ->default(300),
                Forms\Components\TextInput::make('kind')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('domain')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('country')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('subcategory')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('specific')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('extra')
                    ->required()
                    ->numeric()
                    ->default(1),
                
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
                Tables\Columns\TextColumn::make('speed')
                    ->label('Speed (m/s)')
                    ->numeric(),
                Tables\Columns\TextColumn::make('range')
                    ->label('Range (km)')
                    ->numeric(),
                Tables\Columns\TextColumn::make('kind')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('domain')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subcategory')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('specific')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('extra')
                    ->numeric()
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
            'index' => Pages\ListWeapons::route('/'),
            'create' => Pages\CreateWeapon::route('/create'),
            'edit' => Pages\EditWeapon::route('/{record}/edit'),
        ];
    }
}
