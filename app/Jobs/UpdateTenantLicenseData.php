<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Settings\LicenseSettings;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use App\DataTransferObjects\LicenseManagement\LicenseData;

class UpdateTenantLicenseData implements ShouldQueue, NotTenantAware
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public function __construct(
        public Tenant $tenant,
        public LicenseData $data,
    ) {}

    public function handle(): void
    {
        $licenseData = $this->data;

        $this->tenant->execute(function () use ($licenseData) {
            $licenseSettings = app(LicenseSettings::class);

            // TODO: Determine how to handle and retrieve this key
            $licenseSettings->license_key = 'ABCD-1234-EFGH-5678';

            $licenseSettings->data = $licenseData;

            $licenseSettings->save();
        });
    }
}
