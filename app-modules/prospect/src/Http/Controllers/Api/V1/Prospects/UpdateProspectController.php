<?php

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
            'sms_opt_out' => ['sometimes', 'boolean'],
            'email_bounce' => ['sometimes', 'boolean'],
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
