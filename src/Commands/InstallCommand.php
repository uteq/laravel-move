<?php

namespace Uteq\Move\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:install {--teams : Indicates if team support should be installed} {--skip-jetstream}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Move and Jetstream components and resources';

    public function handle(): void
    {
        if (! $this->option('skip-jetstream')) {
            $this->line('Installing Jetstream. This can take a while');

            Artisan::call('jetstream:install', ['stack' => 'livewire', '--teams' => $this->option('teams')]);

            echo Artisan::output();
        }


        $this->line('Installing Move');

        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                "flatpickr" => "^4.6.6",
                "imask" => "^6.0.5",
                "jquery" => "^3.5.1",
                "select2" => "^4.0.13",
            ] + $packages;
        });

        // Service Providers...
        copy(__DIR__.'/../../stubs/app/Providers/MoveServiceProvider.php', app_path('Providers/MoveServiceProvider.php'));

        $this->installMoveServiceProvider();

        // Layouts...
        (new Filesystem())->copyDirectory(__DIR__.'/../../stubs/resources/views/layouts', resource_path('views/layouts'));

        // Assets...
        copy(__DIR__.'/../../stubs/public/css/app.css', public_path('css/app.css'));
        copy(__DIR__.'/../../stubs/resources/css/app.css', resource_path('css/app.css'));

        $this->line('Move scaffolding installed successfully');
        $this->line('Please execute the "npm install && npm run dev" command to build your assets');
    }

    /**
     * Install the Jetstream service providers in the application configuration file.
     *
     * @return void
     */
    protected function installMoveServiceProvider()
    {
        if (! Str::contains($appConfig = file_get_contents(config_path('app.php')), 'App\\Providers\\MoveServiceProvider::class')) {
            file_put_contents(config_path('app.php'), str_replace(
                "App\\Providers\JetstreamServiceProvider::class,",
                "App\\Providers\JetstreamServiceProvider::class,".PHP_EOL."        App\Providers\MoveServiceProvider::class,",
                $appConfig
            ));
        }
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }
}
