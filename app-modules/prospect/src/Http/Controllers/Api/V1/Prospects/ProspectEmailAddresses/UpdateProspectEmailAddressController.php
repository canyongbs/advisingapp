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
