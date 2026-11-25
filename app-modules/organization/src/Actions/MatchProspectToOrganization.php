<?php

namespace AdvisingApp\Organization\Actions;

use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Organization\Models\Scopes\HasDomain;
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

        DB::transaction(function () use ($prospect): void {
            $prospect = Prospect::query()
                ->whereKey($prospect->getKey())
                ->whereNull('organization_id')
                ->lockForUpdate()
                ->first();

            if (! $prospect) {
                return;
            }

            [$primaryEmailAddresses, $additionalEmailAddresses] = $prospect->emailAddresses
                ->partition(fn (ProspectEmailAddress $emailAddress): bool => $emailAddress->getKey() === $prospect->primary_email_id);

            foreach ($primaryEmailAddresses->concat($additionalEmailAddresses) as $emailAddress) {
                $organization = Organization::query()
                    ->tap(new HasDomain(Str::afterLast($emailAddress->address, '@')))
                    ->first();

                if (! $organization) {
                    continue;
                }

                $prospect->organization()->associate($organization);
                $prospect->save();

                return;
            }
        });
    }
}
