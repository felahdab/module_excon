<?php

namespace Modules\Excon\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

use App\Filament\PanelRegistry\DirectMenuItem;
use App\Filament\PanelRegistry\PanelRegistry;

use Modules\Excon\Models\Engagement;
use Modules\Excon\Models\Position;
use Modules\Excon\Models\Side;
use Modules\Excon\Models\Unit;
use Modules\Excon\Models\Weapon;
use Modules\Excon\Models\Identifier;


use Modules\Excon\Policies\EngagementPolicy;
use Modules\Excon\Policies\PositionPolicy;
use Modules\Excon\Policies\SidePolicy;
use Modules\Excon\Policies\UnitPolicy;
use Modules\Excon\Policies\WeaponPolicy;
use Modules\Excon\Policies\IdentifierPolicy;

use Modules\Excon\Filament\Pages\Dashboard;

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
        app(PanelRegistry::class)->registerDirectMenuItem(
            DirectMenuItem::make()
                ->name('Excon')
                ->url(fn() => Dashboard::getUrl())
        );
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
