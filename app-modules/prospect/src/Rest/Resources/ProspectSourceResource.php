<?php

namespace Assist\Prospect\Rest\Resources;

use Lomkit\Rest\Relations\HasMany;
use App\Rest\Resource as RestResource;
use Assist\Prospect\Models\ProspectSource;
use Lomkit\Rest\Http\Requests\RestRequest;

class ProspectSourceResource extends RestResource
{
    public static $model = ProspectSource::class;

    public function fields(RestRequest $request): array
    {
        return [
            'id',
            'name',
            'created_at',
            'updated_at',
        ];
    }

    public function createRules(RestRequest $request): array
    {
        return [
            'id' => ['missing'],
            'name' => ['required', 'string', 'unique:prospect_sources,name', 'max:255'],
            'created_at' => ['missing'],
            'updated_at' => ['missing'],
        ];
    }

    public function updateRules(RestRequest $request): array
    {
        return [
            'id' => ['missing'],
            'name' => ['string', 'unique:prospect_sources,name', 'max:255'],
            'created_at' => ['missing'],
            'updated_at' => ['missing'],
        ];
    }

    public function relations(RestRequest $request): array
    {
        return [
            HasMany::make('prospects', ProspectResource::class),
        ];
    }
}
