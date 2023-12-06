<?php

namespace App\Console\Commands;

use InterNACHI\Modular\Console\Commands\Make\Modularize;
use Lomkit\Rest\Console\Commands\ControllerCommand as BaseControllerCommand;

class ControllerCommand extends BaseControllerCommand
{
    use Modularize;
}
