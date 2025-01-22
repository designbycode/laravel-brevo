<?php

namespace Designbycode\LaravelBrevo\Commands;

use Illuminate\Console\Command;

class BrevoCommand extends Command
{
    public $signature = 'laravel-brevo';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
