<?php

namespace App\Console\Commands;

use InterNACHI\Modular\Console\Commands\Make\Modularize;
use Lomkit\Rest\Console\Commands\ResourceCommand as BaseResourceCommand;

class ResourceCommand extends BaseResourceCommand
{
    use Modularize;
}
