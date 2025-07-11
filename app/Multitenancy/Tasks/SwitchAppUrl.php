<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace App\Multitenancy\Tasks;

use App\Models\Tenant;
use Exception;
use Illuminate\Support\Facades\URL;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SwitchAppUrl implements SwitchTenantTask
{
    public function __construct(
        protected ?string $originalUrl = null,
    ) {
        $this->originalUrl ??= config('app.url');
    }

    public function makeCurrent(IsTenant $tenant): void
    {
        throw_if(
            ! $tenant instanceof Tenant,
            new Exception('Tenant is not an instance of Tenant')
        );

        // We may want to look into defining whether we want to use https at the tenant level
        $scheme = parse_url($this->originalUrl)['scheme'];

        $this->setAppUrl("{$scheme}://{$tenant->domain}");

        URL::useOrigin(config('app.url'));
    }

    public function forgetCurrent(): void
    {
        $this->setAppUrl($this->originalUrl);

        URL::useOrigin($this->originalUrl);
    }

    protected function setAppUrl(string $url): void
    {
        config([
            'app.url' => $url,
        ]);
    }
}
