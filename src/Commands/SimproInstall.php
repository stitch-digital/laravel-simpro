<?php

namespace StitchDigital\Simpro\Commands;

use Illuminate\Console\Command;

class SimproInstall extends Command
{
    public $signature = 'laravel-simpro';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
