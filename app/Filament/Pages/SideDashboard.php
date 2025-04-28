<?php

namespace Modules\Excon\Filament\Pages;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Htmlable;

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
use Modules\Excon\Models\Side;
use Modules\Excon\Models\Unit;
use Modules\Excon\Models\Weapon;
use Modules\Excon\Models\Engagement;
use Modules\Excon\Models\EntityNumber;

class SideDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'excon::filament.pages.side-dashboard';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'side-dashboard/{sideid}';

    public ?array $data = [];
    public ?Side $side;

    public function mount(?int $sideid = null): void
    {
        $this->form->fill();
        $this->side = Side::findOrFail($sideid);
    }

    public function getTitle(): string | Htmlable
    {
        return Str::ucfirst($this->side?->name) . ' dashboard';
    }

    public static function canAccess(): bool
    {
        $sideid = intval(request('sideid'));
        $side = Unit::findOrFail($sideid);

        $result = auth()->check() && ( 
                cast_as_eloquent_descendant(auth()->user(), User::class)->side?->id == $side->id
                || auth()->user()->can("excon::view_all_sides_dashboard") 
                );

                    
        return $result;
    }

    protected function getHeaderActions(): array
    {

        return [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data');
    }

    public function unitsInfolist(Infolist $infolist): Infolist
    {
        $ret = $infolist
            ->record($this->side)
            ->columns(2)
            ->schema([
                Section::make('Weapons')
                    ->description('Current ammunition available')
                    ->columnSpan(1)
                    ->columns(2)
                    ->schema([
                        RepeatableEntry::make('units')
                        ->label(false)
                        ->columnSpan(2)
                        ->columns(2)
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('available_weapons'),
                        ])
                    ]),
            ]);
        
        return $ret;
    }
}
