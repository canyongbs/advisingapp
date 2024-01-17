<?php

namespace App\Multitenancy\DataTransferObjects;

use Spatie\LaravelData\Data;

class TenantConfig extends Data
{
    public function __construct(
        public TenantDatabaseConfig $database,
        public TenantSisDatabaseConfig $sisDatabase,
        public TenantS3FilesystemConfig $s3Filesystem,
        public TenantS3FilesystemConfig $s3PublicFilesystem,
        public TenantMailConfig $mail,
    ) {}
}
