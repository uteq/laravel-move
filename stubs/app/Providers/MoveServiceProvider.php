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
        Move::useSidebarGroups(true);
        Move::resourceNamespace('App\\Move');
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
