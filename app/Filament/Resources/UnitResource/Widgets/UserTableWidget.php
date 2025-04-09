<?php

namespace Modules\Excon\Filament\Resources\UnitResource\Widgets;

use Illuminate\Database\Eloquent\Model;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Toggle;


use Modules\Excon\Models\User;
use Modules\Excon\Events\AffectUserToUnitEvent;

class UserTableWidget extends BaseWidget
{
    public ?Model $record = null;

    public function table(Table $table): Table
    {
        $unit=$this->record;

        return $table
            ->query(
                //User::whereIn("id", $this->record->users->pluck('id'))
                User::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('nom'),
                Tables\Columns\TextColumn::make('prenom'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('unit.name'),
            ])
            ->actions([
                Tables\Actions\Action::make("affecter")
                    ->requiresConfirmation()
                    ->visible(function($record) use ($unit)
                    {
                        return auth()->check() && auth()->user()->can('excon::affect_users') && $record->unit?->id != $unit->id;
                    })
                    ->action(function($record) use($unit){
                        AffectUserToUnitEvent::dispatch($record, $unit);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make("affecter")
                    ->requiresConfirmation()
                    ->action(function($records) use($unit){
                        foreach ($records as $record){
                            AffectUserToUnitEvent::dispatch($record, $unit);
                        }
                        
                    })
            ])
            ->filters([
                Filter::make('limit_to_this_unit')
                    ->form([
                        Toggle::make('affecte_to_this_unit')
                            ->label("See only users assigned to this unit"),
                    ])
                    ->query(function (Builder $query, array $data) use ($unit): Builder {
                        return $query
                            ->when(
                                $data['affecte_to_this_unit'],
                                function (Builder $query, $restrain) use ($unit) { return $query->whereIn('id', $unit->users->pluck('id')); },
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['affecte_to_this_unit']) {
                            return null;
                        }
                
                        return 'Utilisateurs affectés';
                    })
                ]
            );
    }
}
