<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Prospect\Http\Controllers\Api\V1\Prospects;

use AdvisingApp\Prospect\Actions\UpdateProspect;
use AdvisingApp\Prospect\DataTransferObjects\UpdateProspectData;
use AdvisingApp\Prospect\Http\Resources\Api\V1\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Http\Controllers\Api\Concerns\CanIncludeRelationships;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateProspectController
{
    use CanIncludeRelationships;

    /**
     * @response ProspectResource
     */
    #[Group('Prospects')]
    #[QueryParameter('include', description: 'Include related resources in the response.', type: 'string', examples: [
        'email_addresses' => new Example('email_addresses'),
        'primary_email_address' => new Example('primary_email_address'),
    ])]
    public function __invoke(Request $request, UpdateProspect $updateProspect, Prospect $prospect): JsonResource
    {
        Gate::authorize('viewAny', Prospect::class);
        Gate::authorize('update', $prospect);

        $data = $request->validate([
            'first_name' => ['sometimes', 'max:255'],
            'last_name' => ['sometimes', 'max:255'],
            'full_name' => ['sometimes', 'max:255'],
            'preferred' => ['sometimes', 'max:255'],
            'description' => ['sometimes', 'max:65535'],
            'status' => ['sometimes', 'max:255'],
            'source' => ['sometimes', 'max:255'],
            'birthdate' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'hsgrad' => ['sometimes', 'numeric'],
            'email_addresses' => ['sometimes', 'array'],
            'email_addresses.*' => ['array'],
            'email_addresses.*.address' => [
                'required',
                'email',
                Rule::unique('prospect_email_addresses', 'address')->whereNot('prospect_id', $prospect->getKey()),
            ],
            'email_addresses.*.type' => ['sometimes', 'max:255'],
            'primary_email_id' => ['sometimes', 'uuid:4', Rule::exists(ProspectEmailAddress::class, 'id')->where('prospect_id', $prospect->getKey())],
        ]);

        $status = isset($data['status'])
            ? ProspectStatus::whereRaw('LOWER(name) = ?', [Str::lower($data['status'])])->first()
            : null;

        if (isset($data['status']) && ! $status) {
            throw ValidationException::withMessages(['status' => 'Status does not exist.']);
        }

        $source = isset($data['source'])
            ? ProspectSource::whereRaw('LOWER(name) = ?', [Str::lower($data['source'])])->first()
            : null;

        if (isset($data['source']) && ! $source) {
            throw ValidationException::withMessages(['source' => 'Source does not exist.']);
        }

        $data = UpdateProspectData::from([
            ...$data,
            'status' => $status,
            'source' => $source,
        ]);

        $prospect = $updateProspect->execute($prospect, $data);

        return $prospect
            ->withoutRelations()
            ->load($this->getIncludedRelationshipsToLoad($request, [
                'email_addresses' => 'emailAddresses',
                'primary_email_address' => 'primaryEmailAddress',
            ]))
            ->toResource(ProspectResource::class);
    }
}
