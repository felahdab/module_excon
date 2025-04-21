<?php

namespace Modules\Excon\Filament\Resources\SideResource\Widgets;

use Illuminate\Database\Eloquent\Model;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Toggle;


use Modules\Excon\Models\User;
use Modules\Excon\Events\AffectUserToSideEvent;

class UserTableWidget extends BaseWidget
{
    public ?Model $record = null;

    public function table(Table $table): Table
    {
        $side=$this->record;

        return $table
            ->query(
                //User::whereIn("id", $this->record->users->pluck('id'))
                User::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('nom'),
                Tables\Columns\TextColumn::make('prenom'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('side.name'),
            ])
            ->actions([
                Tables\Actions\Action::make("affecter")
                    ->requiresConfirmation()
                    ->visible(function($record) use ($side)
                    {
                        return auth()->check() && 
                                auth()->user()->can('excon::affect_users') && 
                                $record->side?->id != $side->id;
                    })
                    ->action(function($record) use($side){
                        AffectUserToSideEvent::dispatch($record, $side);
                    }),
                Tables\Actions\Action::make("de-affecter")
                    ->requiresConfirmation()
                    ->visible(function($record) use ($side)
                    {
                        return auth()->check() && 
                                auth()->user()->can('excon::affect_users') 
                                && $record->side?->id != null
                                && $record->unit == null;
                    })
                    ->action(function($record) {
                        AffectUserToSideEvent::dispatch($record, null);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make("affecter")
                    ->requiresConfirmation()
                    ->action(function($records) use($side){
                        foreach ($records as $record){
                            AffectUserToSideEvent::dispatch($record, $side);
                        }
                        
                    })
            ])
            ->filters([
                Filter::make('limit_to_this_side')
                    ->form([
                        Toggle::make('affecte_to_this_side')
                            ->label("See only users assigned to this side"),
                    ])
                    ->query(function (Builder $query, array $data) use ($side): Builder {
                        return $query
                            ->when(
                                $data['affecte_to_this_side'],
                                function (Builder $query, $restrain) use ($side) { return $query->whereIn('id', $side->users->pluck('id')); },
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['affecte_to_this_side']) {
                            return null;
                        }
                
                        return 'Utilisateurs affectés';
                    })
                ]
            );
    }
}
