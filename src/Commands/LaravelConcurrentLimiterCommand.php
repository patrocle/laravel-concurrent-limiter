<?php

namespace Patrocle\LaravelConcurrentLimiter\Commands;

use Illuminate\Console\Command;

class LaravelConcurrentLimiterCommand extends Command
{
    public $signature = 'laravel-concurrent-limiter';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
