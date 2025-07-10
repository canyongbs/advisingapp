<?php

namespace AdvisingApp\Prospect\Http\Controllers\Api\V1\Prospects\ProspectEmailAddresses;

use AdvisingApp\Prospect\Actions\DeleteProspectEmailAddress;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class DeleteProspectEmailAddressController
{
    #[Group('Prospects')]
    public function __invoke(DeleteProspectEmailAddress $deleteProspectEmailAddress, Prospect $prospect, ProspectEmailAddress $prospectEmailAddress): Response
    {
        Gate::authorize('viewAny', Prospect::class);
        Gate::authorize('update', $prospect);

        $deleteProspectEmailAddress->execute($prospectEmailAddress);

        return response()->noContent(204);
    }
}
