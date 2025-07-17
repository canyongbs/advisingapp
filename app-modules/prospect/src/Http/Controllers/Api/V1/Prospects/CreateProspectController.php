<?php

namespace AdvisingApp\Prospect\Http\Controllers\Api\V1\Prospects;

use AdvisingApp\Prospect\Actions\CreateProspect;
use AdvisingApp\Prospect\DataTransferObjects\CreateProspectData;
use AdvisingApp\Prospect\Http\Resources\Api\V1\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
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
use Illuminate\Validation\ValidationException;

class CreateProspectController
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
    public function __invoke(Request $request, CreateProspect $createProspect): JsonResource
    {
        Gate::authorize('viewAny', Prospect::class);
        Gate::authorize('create', Prospect::class);

        $data = $request->validate([
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'full_name' => ['required', 'max:255'],
            'preferred' => ['sometimes', 'max:255'],
            'description' => ['sometimes', 'max:65535'],
            'sms_opt_out' => ['sometimes', 'boolean'],
            'email_bounce' => ['sometimes', 'boolean'],
            'status' => ['required', 'max:255'],
            'source' => ['required', 'max:255'],
            'birthdate' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'hsgrad' => ['sometimes', 'numeric'],
            'email_addresses' => ['sometimes', 'array'],
            'email_addresses.*' => ['array'],
            'email_addresses.*.address' => ['required', 'email', 'unique:prospect_email_addresses,address'],
            'email_addresses.*.type' => ['sometimes', 'max:255'],
        ]);

        $status = ProspectStatus::whereRaw('LOWER(name) = ?', [Str::lower($data['status'])])->first();
        throw_if(! $status, ValidationException::withMessages(['status' => 'Status does not exist.']));
        $source = ProspectSource::whereRaw('LOWER(name) = ?', [Str::lower($data['source'])])->first();
        throw_if(! $source, ValidationException::withMessages(['source' => 'Source does not exist.']));

        $data = CreateProspectData::from([
            ...$data,
            'status' => $status->getKey(),
            'source' => $source->getKey(),
        ]);

        $prospect = $createProspect->execute($data);

        return $prospect
            ->withoutRelations()
            ->load($this->getIncludedRelationshipsToLoad($request, [
                'email_addresses' => 'emailAddresses',
                'primary_email_address' => 'primaryEmailAddress',
            ]))
            ->toResource(ProspectResource::class);
    }
}
