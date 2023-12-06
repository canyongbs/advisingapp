<?php

namespace Assist\Prospect\Rest\Resources;

use Assist\Prospect\Models\Prospect;
use Lomkit\Rest\Relations\BelongsTo;
use App\Rest\Resource as RestResource;
use Lomkit\Rest\Http\Requests\RestRequest;

class ProspectResource extends RestResource
{
    public static $model = Prospect::class;

    public function fields(RestRequest $request): array
    {
        return [
            'id',
            'first_name',
            'last_name',
            'full_name',
            'preferred',
            'description',
            'email',
            'email_2',
            'mobile',
            'sms_opt_out',
            'email_bounce',
            'phone',
            'address',
            'address_2',
            'birthdate',
            'hsgrad',
            'created_at',
            'updated_at',
        ];
    }

    public function createRules(RestRequest $request): array
    {
        return [
            'id' => ['missing'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'preferred' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'email' => ['nullable', 'email', 'max:255'],
            'email_2' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'sms_opt_out' => ['boolean'],
            'email_bounce' => ['boolean'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'hsgrad' => ['nullable', 'integer', 'digits:4'],
            'created_at' => ['missing'],
            'updated_at' => ['missing'],
        ];
    }

    public function updateRules(RestRequest $request): array
    {
        return [
            'id' => ['missing'],
            'first_name' => ['string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'full_name' => ['string', 'max:255'],
            'preferred' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'email' => ['nullable', 'email', 'max:255'],
            'email_2' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'sms_opt_out' => ['boolean'],
            'email_bounce' => ['boolean'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'hsgrad' => ['nullable', 'integer', 'digits:4'],
            'created_at' => ['missing'],
            'updated_at' => ['missing'],
        ];
    }

    public function relations(RestRequest $request): array
    {
        return [
            BelongsTo::make('status', ProspectStatusResource::class)->requiredOnCreation(),
            BelongsTo::make('source', ProspectSourceResource::class)->requiredOnCreation(),
        ];
    }

    public function scopes(RestRequest $request): array
    {
        return [];
    }
}
