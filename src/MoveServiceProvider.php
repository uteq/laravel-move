<?php

namespace Uteq\Move;

use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use Uteq\Move\Commands\MoveCommand;
use Uteq\Move\Controllers\DownloadController;
use Uteq\Move\Controllers\MoveJavaScriptAssets;
use Uteq\Move\Controllers\MoveStyleAssets;
use Uteq\Move\Controllers\PreviewFileController;
use Uteq\Move\Facades\Move;
use Uteq\Move\Livewire\HeaderSearch;
use Uteq\Move\Livewire\ResourceForm;
use Uteq\Move\Livewire\ResourceShow;
use Uteq\Move\Livewire\ResourceTable;

class MoveServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->configurePublishers();

            $this->commands([
                MoveCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'move');
        $this->configureNamespaces();
        $this->configureComponents();
        $this->configureRoutes();
        $this->configureBladeDirectives();
    }

    public function configurePublishers()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-move.php' => config_path('laravel-move.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views/vendor/laravel-move'),
        ], 'views');

        $migrationFileName = 'create_laravel_move_table.php';
        if (! $this->migrationFileExists($migrationFileName)) {
            $this->publishes([
                __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFileName),
            ], 'migrations');
        }
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);

        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }

    public function configureNamespaces()
    {
        if (file_exists(Move::generatePathFromNamespace('App\\Move'))) {
            Move::resourceNamespace('App\\Move', '');
        }

        foreach (Move::all() as $alias => $class) {
            $this->app->singleton(Move::getPrefix() . '.' . $alias, function () use ($class) {
                $model = $class::$model;

                return new $class(new $model());
            });
        }
    }

    public function configureComponents()
    {
        $this->callAfterResolving(BladeCompiler::class, function () {
            $this->registerComponent('field.checkbox');
            $this->registerComponent('field.date');
            $this->registerComponent('field.input');
            $this->registerComponent('field.number');
            $this->registerComponent('field.password');
            $this->registerComponent('field.select');

            $this->registerComponent('form.input-error');
            $this->registerComponent('form.label');
            $this->registerComponent('form.row');

            $this->registerComponent('sidebar.link');

            $this->registerComponent('table.filters');
            $this->registerComponent('table.header');
            $this->registerComponent('table.item-actions');

            $this->registerComponent('a');
            $this->registerComponent('action-message');
            $this->registerComponent('button');
            $this->registerComponent('card');
            $this->registerComponent('dialog-modal');
            $this->registerComponent('dropdown');
            $this->registerComponent('dropdown-link');
            $this->registerComponent('form-section');
            $this->registerComponent('modal');
            $this->registerComponent('row');
            $this->registerComponent('secondary-button');
            $this->registerComponent('section-title');
            $this->registerComponent('status');
            $this->registerComponent('switchable-team');
            $this->registerComponent('table');
            $this->registerComponent('td');
            $this->registerComponent('th');
        });
    }

    /**
     * Register the given component.
     *
     * @param string $component
     * @return void
     */
    protected function registerComponent(string $component)
    {
        Blade::component('move::components.' . $component, 'move-' . $component);
    }

    public function configureRoutes()
    {
        Route::get('/move/move.js', [MoveJavaScriptAssets::class, 'source']);
        Route::get('/move/move.css', [MoveStyleAssets::class, 'source']);

        Route::bind('model', function ($value) {
            $resource = Move::resolveResource(request()->route()->parameter('resource'));

            return $resource->model()::find($value) ?: $resource::newModel();
        });

        Route::group(['middleware' => Move::routeMiddlewares()], function () {
            Route::prefix(Move::getPrefix())->group(function () {
                Route::get('preview-file/{filename}', PreviewFileController::class)
                    ->name('move.preview-file');

                // Download
                Route::get('download', DownloadController::class)
                    ->name('move.download')
                    ->middleware(ValidateSignature::class);

                // Resources
                Route::get('{resource}/create', ResourceForm::class)
                    ->where('resource', '([^0-9]*)')
                    ->name('move.create');

                Route::get('{resource}/{model}/edit', ResourceForm::class)
                    ->where('resource', '([^0-9]*)')
                    ->name('move.edit');

                Route::get('{resource}/{model}/show', ResourceShow::class)
                    ->where('resource', '([^0-9]*)')
                    ->name('move.show');

                Route::get('{resource}', ResourceTable::class)
                    ->where('resource', '(.*)')
                    ->name('move.index');
            });
        });
    }

    protected function configureBladeDirectives()
    {
        Blade::directive('moveStyles', [MoveBladeDirectives::class, 'moveStyles']);
        Blade::directive('moveScripts', [MoveBladeDirectives::class, 'moveScripts']);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-move.php', 'laravel-move');

        $this->app->singleton('move', \Uteq\Move\Move::class);

        $this->configureLivewire();
    }

    public function configureLivewire()
    {
        $this->app->afterResolving(BladeCompiler::class, function () {
            if (! class_exists(Livewire::class)) {
                return;
            }

            Livewire::component('livewire.resource-table', ResourceTable::class);
            Livewire::component('livewire.resource-show', ResourceShow::class);
            Livewire::component('livewire.resource-form', ResourceForm::class);

            Livewire::component('header-search', HeaderSearch::class);
        });
    }
}
