<?php

namespace Modules\Excon\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

use App\Filament\PanelRegistry\DirectMenuItem;
use App\Filament\PanelRegistry\ModuleDefinedMenusRegistry;

use Modules\Excon\Models\Engagement;
use Modules\Excon\Models\Position;
use Modules\Excon\Models\Side;
use Modules\Excon\Models\Unit;
use Modules\Excon\Models\Weapon;
use Modules\Excon\Models\Identifier;

use Modules\Excon\Enums\UnitTypes;

use Modules\Excon\Policies\EngagementPolicy;
use Modules\Excon\Policies\PositionPolicy;
use Modules\Excon\Policies\SidePolicy;
use Modules\Excon\Policies\UnitPolicy;
use Modules\Excon\Policies\WeaponPolicy;
use Modules\Excon\Policies\IdentifierPolicy;

use Modules\Excon\Filament\Pages\Dashboard;
use Modules\Excon\Filament\Pages\MyUnitDashboard;
use Modules\Excon\Filament\Pages\UnitDashboard;

class ExconServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Excon';

    protected string $nameLower = 'excon';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->registerPolicies();
        $this->registerMenus();
    }

    public function registerMenus()
    {
        $blue_unit_dashboard_menus = [];
        foreach (Unit::notOfType(UnitTypes::STAFF->value)
                        ->where('side_id', Side::where('name', 'blue')->first()->id)
                        ->orderBy('name')
                        ->get() as $unit)
        {
            $blue_unit_dashboard_menus[] =  DirectMenuItem::make()
                                    ->name("{$unit->name} dashboard")
                                    ->url(fn() => UnitDashboard::getUrl(['unit' => $unit]));
        }

        $red_unit_dashboard_menus = [];
        foreach (Unit::notOfType(UnitTypes::STAFF->value)
                        ->where('side_id', Side::where('name', 'red')->first()->id)
                        ->orderBy('name')
                        ->get() as $unit)
        {
            $red_unit_dashboard_menus[] =  DirectMenuItem::make()
                                    ->name("{$unit->name} dashboard")
                                    ->url(fn() => UnitDashboard::getUrl(['unit' => $unit]));
        }

        app(ModuleDefinedMenusRegistry::class)->registerDirectMenuItems([
            DirectMenuItem::make()
                ->name('Excon Module')
                ->visible(fn() => auth()->check() && Dashboard::canAccess())
                ->children([
                    DirectMenuItem::make()
                        ->name('Excon - Dashboard')
                        ->url(fn() => Dashboard::getUrl(panel: "excon"))
                        ->visible(fn() => auth()->check() && Dashboard::canAccess()),
                    DirectMenuItem::make()
                        ->name('Excon - My unit dashboard')
                        ->url(fn() => MyUnitDashboard::getUrl(panel: "excon"))
                        ->visible(fn() => auth()->check() && MyUnitDashboard::canAccess()),
                    
                ]),
                DirectMenuItem::make()
                    ->name('Excon Blue units dashboards')
                    ->visible(fn() => auth()->check() && auth()->user()->can("excon::view_all_units_dashboard"))
                    ->children($blue_unit_dashboard_menus),
                DirectMenuItem::make()
                    ->name('Excon Red units dashboards')
                    ->visible(fn() => auth()->check() && auth()->user()->can("excon::view_all_units_dashboard"))
                    ->children($red_unit_dashboard_menus)
        ]);
    }

    public function registerPolicies()
    {
        $policies = [
            Engagement::class => EngagementPolicy::class,
            Position::class => PositionPolicy::class,
            Side::class => SidePolicy::class,
            Unit::class => UnitPolicy::class,
            Weapon::class => WeaponPolicy::class,
            Identifier::class => IdentifierPolicy::class

        ];

        foreach ($policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->name, 'config/config.php') => config_path($this->nameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), $this->nameLower);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
