<?php

namespace AdvisingApp\DataMigration\Jobs;

use Spatie\Multitenancy\Jobs\NotTenantAware;

class LandlordOneTimeOperationProcessJob extends OneTimeOperationProcessJob implements NotTenantAware {}
