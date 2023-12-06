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

    public function relations(RestRequest $request): array
    {
        return [
            BelongsTo::make('status', ProspectStatusResource::class),
            BelongsTo::make('source', ProspectSourceResource::class),
        ];
    }

    public function scopes(RestRequest $request): array
    {
        return [];
    }
}
