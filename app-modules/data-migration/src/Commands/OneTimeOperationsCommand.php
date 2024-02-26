<?php

namespace AdvisingApp\DataMigration\Commands;

use Illuminate\Console\Command;
use AdvisingApp\DataMigration\OneTimeOperationManager;
use AdvisingApp\DataMigration\Commands\Utils\ColoredOutput;

abstract class OneTimeOperationsCommand extends Command
{
    use ColoredOutput;

    public const LABEL_PROCESSED = 'PROCESSED';

    public const LABEL_PENDING = 'PENDING';

    public const LABEL_DISPOSED = 'DISPOSED';

    protected string $operationsDirectory;

    public function __construct()
    {
        parent::__construct();

        $this->operationsDirectory = OneTimeOperationManager::getDirectoryPath();
    }
}
