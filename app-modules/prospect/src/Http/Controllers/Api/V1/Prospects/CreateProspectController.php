<?php

namespace AdvisingApp\Prospect\Http\Controllers\Api\V1\Prospects;

use AdvisingApp\Prospect\Actions\CreateProspect;
use AdvisingApp\Prospect\DataTransferObjects\CreateProspectData;
use AdvisingApp\Prospect\Http\Resources\Api\V1\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use App\Http\Controllers\Api\Concerns\CanIncludeRelationships;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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
            'sisid' => ['required', 'max:255', 'alpha_dash', Rule::unique('students', 'sisid')],
            'otherid' => ['sometimes', 'max:255'],
            'first' => ['required', 'max:255'],
            'last' => ['required', 'max:255'],
            'full_name' => ['required', 'max:255'],
            'preferred' => ['sometimes', 'max:255'],
            'birthdate' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'hsgrad' => ['sometimes', 'numeric'],
            'gender' => ['sometimes', 'max:255'],
            'sms_opt_out' => ['sometimes', 'boolean'],
            'email_bounce' => ['sometimes', 'boolean'],
            'dual' => ['sometimes', 'boolean'],
            'ferpa' => ['sometimes', 'boolean'],
            'firstgen' => ['sometimes', 'boolean'],
            'sap' => ['sometimes', 'boolean'],
            'holds' => ['sometimes', 'max:255'],
            'dfw' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'ethnicity' => ['sometimes', 'max:255'],
            'lastlmslogin' => ['sometimes', 'date', 'date_format:Y-m-d H:i:s'],
            'f_e_term' => ['sometimes', 'max:255'],
            'mr_e_term' => ['sometimes', 'max:255'],
            'email_addresses' => ['sometimes', 'array'],
            'email_addresses.*' => ['array'],
            'email_addresses.*.address' => ['required', 'email'],
            'email_addresses.*.type' => ['sometimes', 'max:255'],
        ]);

        $prospect = $createProspect->execute(CreateProspectData::from($data));

        return $prospect
            ->withoutRelations()
            ->load($this->getIncludedRelationshipsToLoad($request, [
                'email_addresses' => 'emailAddresses',
                'primary_email_address' => 'primaryEmailAddress',
            ]))
            ->toResource(ProspectResource::class);
    }
}
