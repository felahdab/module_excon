<?php

namespace Modules\Excon\Filament\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Modules\Excon\Filament\Resources\IdentifierResource\Pages;
use Modules\Excon\Filament\Resources\IdentifierResource\RelationManagers;
use Modules\Excon\Models\Identifier;
use Modules\Excon\Models\Unit;

class IdentifierResource extends Resource
{
    protected static ?string $model = Identifier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('source')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('identifier')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('unit_id')
                    ->relationship(name: 'unit', titleAttribute: 'name'),
                Forms\Components\Hidden::make('data'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('source')
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.name')
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
                Filter::make('unite')
                    ->form([
                        Select::make('unit_id')
                        ->options(Unit::orderBy('name')->pluck('name', 'id')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['unit_id'],
                                fn (Builder $query, $unit_id): Builder => $query->where('unit_id', $unit_id),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['unit_id']) {
                            return null;
                        }
                
                        return 'Identifiers for unit: ' . Unit::find($data['unit_id'])->name;
                    })
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
            'index' => Pages\ListIdentifiers::route('/'),
            'create' => Pages\CreateIdentifier::route('/create'),
            'edit' => Pages\EditIdentifier::route('/{record}/edit'),
        ];
    }
}
