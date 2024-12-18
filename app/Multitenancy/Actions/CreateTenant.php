<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Multitenancy\Actions;

use AdvisingApp\Theme\DataTransferObjects\ThemeConfig;
use AdvisingApp\Theme\Jobs\UpdateTenantTheme;
use App\DataTransferObjects\LicenseManagement\LicenseData;
use App\Jobs\CreateTenantUser;
use App\Jobs\DispatchTenantSetupCompleteEvent;
use App\Jobs\MigrateTenantDatabase;
use App\Jobs\SeedTenantDatabase;
use App\Jobs\UpdateTenantLicenseData;
use App\Models\Tenant;
use App\Multitenancy\DataTransferObjects\TenantConfig;
use App\Multitenancy\DataTransferObjects\TenantUser;
use App\Multitenancy\Events\NewTenantSetupFailure;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Throwable;

class CreateTenant
{
    public function __invoke(
        string $name,
        string $domain,
        TenantConfig $config,
        ?TenantUser $user = null,
        ?LicenseData $licenseData = null,
        ?ThemeConfig $themeConfig = null,
        bool $seedTenantDatabase = true,
    ): ?Tenant {
        $tenant = Tenant::query()->create([
            'name' => $name,
            'domain' => $domain,
            'key' => $this->generateTenantKey(),
            'config' => $config,
        ]);

        Bus::batch([
            [
                new MigrateTenantDatabase($tenant),
                ...($seedTenantDatabase ? [new SeedTenantDatabase($tenant)] : []),
                ...($licenseData ? [new UpdateTenantLicenseData($tenant, $licenseData)] : []),
                ...($themeConfig ? [new UpdateTenantTheme($tenant, $themeConfig)] : []),
                ...($user ? [new CreateTenantUser($tenant, $user)] : []),
                new DispatchTenantSetupCompleteEvent($tenant),
            ],
        ])
            ->name("deploy-tenant-{$tenant->getKey()}-{$domain}")
            ->onQueue(config('queue.landlord_queue'))
            ->catch(function (Throwable $exception) use ($tenant) {
                Event::dispatch(new NewTenantSetupFailure($tenant, $exception));
            })
            ->dispatch();

        return $tenant;
    }

    protected function generateTenantKey(): string
    {
        return 'base64:' . base64_encode(
            Encrypter::generateKey(config('app.cipher'))
        );
    }
}
