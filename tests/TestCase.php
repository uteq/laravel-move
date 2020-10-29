<?php

namespace Uteq\Move\Tests;

use Actb\BladeGithubOcticons\GithubOcticonsServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Brunocfalcao\BladeFeatherIcons\BladeFeatherIconsServiceProvider;
use Davidhsianturi\BladeBootstrapIcons\BladeBootstrapIconsServiceProvider;
use Hasnayeen\Evaicons\BladeEvaiconsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Khatabwedaa\BladeCssIcons\BladeCssIconsServiceProvider;
use Laravel\Fortify\FortifyServiceProvider;
use Laravel\Jetstream\JetstreamServiceProvider;
use Livewire\LivewireServiceProvider;
use Masterix21\XBladeComponents\XBladeComponentsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use OwenVoke\BladeEntypo\BladeEntypoServiceProvider;
use OwenVoke\BladeFontAwesome\BladeFontAwesomeServiceProvider;
use Skydiver\BladeIconsRemix\BladeIconsRemixServiceProvider;
use Uteq\Move\Facades\Move;
use Uteq\Move\MoveServiceProvider;
use Uteq\Move\ResourceFinder;
use Uteq\Move\Tests\Fixtures\UserResource;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            $this->makeACleanSlate();
        });

        $this->beforeApplicationDestroyed(function () {
            $this->makeACleanSlate();
        });

        parent::setUp();

        $this->app->singleton(ResourceFinder::class, function () {
            return new ResourceFinder(new Filesystem, dirname(dirname(__FILE__)));
        });

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Uteq\\Move\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    public function makeACleanSlate()
    {
        Artisan::call('view:clear');
    }

    protected function getPackageProviders($app)
    {
        return [
            JetstreamServiceProvider::class,
            FortifyServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladeBootstrapIconsServiceProvider::class,
            GithubOcticonsServiceProvider::class,
            BladeEvaiconsServiceProvider::class,
            BladeIconsRemixServiceProvider::class,
            BladeEntypoServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeFontAwesomeServiceProvider::class,
            BladeCssIconsServiceProvider::class,
            BladeFeatherIconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            XBladeComponentsServiceProvider::class,
            LivewireServiceProvider::class,
            MoveServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        Schema::dropAllTables();

        $app['config']->set('view.paths', [
            __DIR__.'/../views',
            resource_path('views'),
        ]);
        $app['config']->set('session.driver', 'file');
        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');

        Move::resource('fixtures.user-resource', UserResource::class);

        include_once __DIR__ . '/../database/migrations/create_move_table.php.stub';
        (new \CreateMoveTable())->up();

        include_once __DIR__ . '/../database/migrations/create_laravel_users_table.php';
        (new \UsersTable())->up();

        include_once __DIR__ . '/../database/migrations/create_laravel_contacts_table.php';
        (new \ContactsTable())->up();
    }
}
