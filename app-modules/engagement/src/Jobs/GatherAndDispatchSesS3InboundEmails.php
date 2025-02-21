<?php

namespace AdvisingApp\Engagement\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class GatherAndDispatchSesS3InboundEmails implements ShouldQueue, NotTenantAware
{
    use Queueable;

    public function __construct() {}

    public function handle(): void
    {
        collect(Storage::disk('s3-inbound-email')->files())
            ->filter(fn (string $file) => $file !== 'AMAZON_SES_SETUP_NOTIFICATION')
            ->each(function (string $file) {
                // This is where we would dispatch a Unique job per file to process the email then delete it
            });
    }
}
