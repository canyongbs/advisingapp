<?php

namespace AdvisingApp\Prospect\Http\Controllers\Api\V1\Prospects;

use AdvisingApp\Prospect\Models\Prospect;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class DeleteProspectController
{
    #[Group('Students')]
    public function __invoke(DeleteProspect $deleteProspect, Prospect $prospect): Response
    {
        Gate::authorize('viewAny', Prospect::class);
        Gate::authorize('delete', $prospect);

        $deleteProspect->execute($prospect);

        return response()->noContent(204);
    }
}
