<?php

namespace AdvisingApp\Engagement\Jobs;

use App\Features\InboundEmailsUpdates;
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
        if (! InboundEmailsUpdates::active()) {
            return;
        }

        collect(Storage::disk('s3-inbound-email')->files())
            ->filter(fn (string $file) => $file !== 'AMAZON_SES_SETUP_NOTIFICATION')
            ->each(function (string $file) {
                dispatch(new ProcessSesS3InboundEmail($file));
            });
    }
}
