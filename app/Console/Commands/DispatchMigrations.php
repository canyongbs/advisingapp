<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Command\Command as CommandAlias;

abstract class DispatchMigrations extends Command
{
    protected function getVersionTag(): string
    {
        $tagProcess = Process::run('git describe --tags --abbrev=0');

        if ($tagProcess->successful()) {
            $tag = rtrim($tagProcess->output());
        } else {
            $this->error($tagProcess->errorOutput());

            return CommandAlias::FAILURE;
        }

        $commitProcess = Process::run('git log --pretty="%h" -n1 HEAD');

        if ($commitProcess->successful()) {
            $shortHash = rtrim($commitProcess->output());
        } else {
            $this->error($commitProcess->errorOutput());

            return CommandAlias::FAILURE;
        }

        return "{$tag}-{$shortHash}";
    }
}
