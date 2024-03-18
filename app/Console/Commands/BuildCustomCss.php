<?php

namespace App\Console\Commands;

use App\Settings\BrandSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class BuildCustomCss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:build-custom-css';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls custom CSS from the database and compiles it into the theme.';

    /**
     * Execute the console command.
     */
    public function handle(BrandSettings $brandSettings): void
    {
        file_put_contents(
            resource_path('css/filament/admin/custom.css'),
            $brandSettings->custom_css ?? '',
        );

        $process = Process::run(
            <<<BASH
                #!/bin/bash
                [ -s "/usr/local/nvm/nvm.sh" ] && \. "/usr/local/nvm/nvm.sh"
                npm run build:vite
            BASH
        )->throw();

        $this->line($process->output());

        $this->info('Custom CSS has been built.');
    }
}
