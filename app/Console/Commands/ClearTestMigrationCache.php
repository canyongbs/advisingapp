<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ClearTestMigrationCache extends Command
{
    protected $signature = 'app:clear-test-migration-cache';

    protected $description = 'Deletes the cache files used for the testing migrations cache.';

    public function handle(): void
    {
        $this->comment('Clearing the testing migration cache...');

        $finder = Finder::create()
            ->in(storage_path('app'))
            ->name('migration-checksum_*.txt')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->files();

        $this->withProgressBar($finder, function (SplFileInfo $file) {
            unlink($file->getPathname());
        });

        $this->newLine();

        $this->info('Testing migration cache cleared!');
    }
}
