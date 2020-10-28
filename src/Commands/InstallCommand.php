<?php

namespace Uteq\Move\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'move:install {--teams : Indicates if team support should be installed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Move and Jetstream components and resources';

    public function handle()
    {
        Artisan::call('jetstream:install', ['stack' => 'livewire', '--team' => $this->option('team')]);

        // TODO install move
        // - Stubs
    }
}
