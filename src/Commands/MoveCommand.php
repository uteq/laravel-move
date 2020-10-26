<?php

namespace Uteq\Move\Commands;

use Illuminate\Console\Command;

class MoveCommand extends Command
{
    public $signature = 'laravel-move';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
