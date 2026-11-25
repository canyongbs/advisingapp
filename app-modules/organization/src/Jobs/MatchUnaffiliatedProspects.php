<?php

namespace AdvisingApp\Organization\Jobs;

use AdvisingApp\Organization\Actions\MatchProspectToOrganization;
use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use App\Features\OrganizationFeature;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MatchUnaffiliatedProspects implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?Organization $organization = null,
    ) {}

    public function handle(MatchProspectToOrganization $matchProspectToOrganization): void
    {
        if (! OrganizationFeature::active()) {
            return;
        }

        $organizations = $this->organization ? [$this->organization] : Organization::query()->get(['id', 'domains'])->all();

        $domains = collect($organizations)
            ->flatMap(fn (Organization $organization): array => array_column($organization->domains ?? [], 'domain'))
            ->map(fn (string $domain): string => Str::lower($domain))
            ->unique()
            ->values()
            ->all();

        if (blank($domains)) {
            return;
        }

        Prospect::query()
            ->whereNull('organization_id')
            ->whereHas('emailAddresses', fn (Builder $query) => $query->whereIn(DB::raw("lower(regexp_replace(address, '^.*@', ''))"), $domains))
            ->chunkById(100, function (Collection $prospects) use ($matchProspectToOrganization): void {
                $prospects->each(fn (Prospect $prospect) => $matchProspectToOrganization($prospect));
            });
    }
}
