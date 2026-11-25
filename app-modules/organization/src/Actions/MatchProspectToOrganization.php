<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Organization\Actions;

use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use App\Features\OrganizationFeature;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MatchProspectToOrganization
{
    public function __invoke(Prospect $prospect): void
    {
        if (! OrganizationFeature::active() || filled($prospect->organization_id)) {
            return;
        }

        $organizationIdsByDomain = Organization::query()
            ->get(['id', 'domains'])
            ->flatMap(fn (Organization $organization): array => collect($organization->domains)
                ->mapWithKeys(fn (array $domain): array => [Str::lower($domain['domain']) => $organization->getKey()])
                ->all())
            ->all();

        [$primaryEmailAddresses, $additionalEmailAddresses] = $prospect->emailAddresses
            ->partition(fn (ProspectEmailAddress $emailAddress): bool => $emailAddress->getKey() === $prospect->primary_email_id);

        $organizationId = $primaryEmailAddresses
            ->concat($additionalEmailAddresses)
            ->map(fn (ProspectEmailAddress $emailAddress): ?string => $organizationIdsByDomain[Str::lower(Str::afterLast($emailAddress->address, '@'))] ?? null)
            ->first(fn (?string $organizationId): bool => filled($organizationId));

        if (blank($organizationId)) {
            return;
        }

        DB::transaction(function () use ($prospect, $organizationId): void {
            $prospect = Prospect::query()
                ->whereKey($prospect->getKey())
                ->whereNull('organization_id')
                ->lockForUpdate()
                ->first();

            if (! $prospect) {
                return;
            }

            $prospect->organization_id = $organizationId;
            $prospect->save();
        });
    }
}
