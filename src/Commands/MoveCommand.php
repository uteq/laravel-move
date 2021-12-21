<?php

namespace Uteq\Move\Commands;

use Illuminate\Console\Command;

class MoveCommand extends Command
{
    public $signature = 'move';

    public $description = 'My command';

    public function handle(): void
    {
        $this->comment('All done');
    }
}
