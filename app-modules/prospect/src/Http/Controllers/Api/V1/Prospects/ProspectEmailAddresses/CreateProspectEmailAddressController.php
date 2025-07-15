<?php

namespace AdvisingApp\Prospect\Http\Controllers\Api\V1\Prospects\ProspectEmailAddresses;

use AdvisingApp\Prospect\Actions\CreateProspectEmailAddress;
use AdvisingApp\Prospect\DataTransferObjects\CreateProspectEmailAddressData;
use AdvisingApp\Prospect\Http\Resources\Api\V1\ProspectEmailAddressResource;
use AdvisingApp\Prospect\Models\Prospect;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CreateProspectEmailAddressController
{
    /**
     * @response ProspectEmailAddressResource
     */
    #[Group('Prospects')]
    public function __invoke(Request $request, CreateProspectEmailAddress $createProspectEmailAddress, Prospect $prospect): JsonResource
    {
        Gate::authorize('viewAny', Prospect::class);
        Gate::authorize('update', $prospect);

        $data = $request->validate([
            'address' => [
                'required',
                'email',
                Rule::unique('prospect_email_addresses', 'address'),
            ],
            'type' => ['sometimes', 'max:255'],
            'order' => ['sometimes', 'integer'],
        ]);

        $prospectEmailAddress = $createProspectEmailAddress->execute($prospect, CreateProspectEmailAddressData::from($data));

        return $prospectEmailAddress->toResource(ProspectEmailAddressResource::class);
    }
}
