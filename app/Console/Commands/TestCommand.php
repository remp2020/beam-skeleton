<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    protected $description = 'Just a test command';

    public function handle(): void
    {
        $this->output->writeln("Hello from test command");
    }
}
