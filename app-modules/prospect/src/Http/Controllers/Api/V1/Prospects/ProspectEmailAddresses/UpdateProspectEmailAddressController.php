<?php

namespace AdvisingApp\Prospect\Http\Controllers\Api\V1\Prospects\ProspectEmailAddresses;

use AdvisingApp\Prospect\Actions\UpdateProspectEmailAddress;
use AdvisingApp\Prospect\DataTransferObjects\UpdateProspectEmailAddressData;
use AdvisingApp\Prospect\Http\Resources\Api\V1\ProspectEmailAddressResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use App\Http\Controllers\Api\Concerns\CanIncludeRelationships;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateProspectEmailAddressController
{
    use CanIncludeRelationships;

    /**
     * @response ProspectEmailAddressResource
     */
    #[Group('Prospects')]
    public function __invoke(Request $request, UpdateProspectEmailAddress $updateProspectEmailAddress, Prospect $prospect, ProspectEmailAddress $prospectEmailAddress): JsonResource
    {
        Gate::authorize('viewAny', Prospect::class);
        Gate::authorize('update', $prospect);

        $data = $request->validate([
            'address' => [
                'sometimes',
                'email',
                Rule::unique('prospect_email_addresses', 'address')->ignore($prospectEmailAddress->getKey()),
            ],
            'type' => ['sometimes', 'max:255'],
            'order' => ['sometimes', 'integer'],
        ]);

        $prospect = $updateProspectEmailAddress->execute($prospectEmailAddress, UpdateProspectEmailAddressData::from($data));

        return $prospect->toResource(ProspectEmailAddressResource::class);
    }
}
