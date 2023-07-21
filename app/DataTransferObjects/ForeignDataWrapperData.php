<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class ForeignDataWrapperData extends Data
{
    public function __construct(
        public string $connection,
        public string $localServerName,
        public string $externalHost,
        public string $externalPort,
        public string $externalUser,
        public string $externalPassword,
        public string $externalDatabase,
        /** @var array<string> */
        public array $tables,
    ) {
    }
}
