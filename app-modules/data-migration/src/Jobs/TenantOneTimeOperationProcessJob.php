<?php

namespace AdvisingApp\DataMigration\Jobs;

use Spatie\Multitenancy\Jobs\NotTenantAware;

class TenantOneTimeOperationProcessJob extends OneTimeOperationProcessJob implements NotTenantAware {}
