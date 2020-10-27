<?php

namespace Uteq\Move\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Uteq\Move\Facades\Move;
use Uteq\Move\MoveServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Move::resourceNamespace('Uteq\\Move\\Tests\\Fixtures', 'fixtures');

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Uteq\\Move\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            MoveServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        /*
        include_once __DIR__.'/../database/migrations/create_laravel_move_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }
}
