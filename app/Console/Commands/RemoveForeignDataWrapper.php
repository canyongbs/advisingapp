<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveForeignDataWrapper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-foreign-data-wrapper';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove foreign data wrapper for SIS database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        resolve(\App\Actions\Setup\RemoveForeignDataWrapper::class)->handle();
    }
}
