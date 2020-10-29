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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Move::prefix('move');
    }
}
