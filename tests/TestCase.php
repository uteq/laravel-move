<?php

namespace Uteq\Move\Tests;

use ContactsTable;
use CreateMoveTable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Laravel\Fortify\FortifyServiceProvider;
use Laravel\Jetstream\JetstreamServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use UsersTable;
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
            $finder = new ResourceFinder(new Filesystem(), dirname(__FILE__));
            $finder->setNamespace('\\Uteq\\Move\\Tests\\');
            $finder->setAppPath(dirname(__FILE__));

            return $finder;
        });

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Uteq\\Move\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    public function makeACleanSlate()
    {
        Artisan::call('view:clear');
    }

    public function getEnvironmentSetUp($app)
    {
        Schema::dropAllTables();

        $app['config']->set('view.paths', [
            __DIR__ . '/../views',
            resource_path('views'),
        ]);
        $app['config']->set('session.driver', 'file');
        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');

        Move::resource('fixtures.user-resource', UserResource::class);
        Move::resource('fixtures.other-namespace.user-resource', Fixtures\OtherNamespace\UserResource::class);

        include_once __DIR__ . '/../database/migrations/create_move_table.php.stub';
        (new CreateMoveTable())->up();

        include_once __DIR__ . '/../database/migrations/create_laravel_users_table.php';
        (new UsersTable())->up();

        include_once __DIR__ . '/../database/migrations/create_laravel_contacts_table.php';
        (new ContactsTable())->up();
    }

    protected function getPackageProviders($app)
    {
        return [
            JetstreamServiceProvider::class,
            FortifyServiceProvider::class,
            LivewireServiceProvider::class,
            MoveServiceProvider::class,
        ];
    }
}
