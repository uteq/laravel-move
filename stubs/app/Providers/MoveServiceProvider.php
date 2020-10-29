<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Uteq\Move\Facades\Move;

class MoveServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Move::prefix('move');

        if (file_exists(Move::generatePathFromNamespace('App\\Move'))) {
            Move::resourceNamespace('App\\Move', '');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
