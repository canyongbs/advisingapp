<?php

namespace Assist\Prospect\Rest\Resources;

use Lomkit\Rest\Relations\HasMany;
use App\Rest\Resource as RestResource;
use Assist\Prospect\Models\ProspectStatus;
use Lomkit\Rest\Http\Requests\RestRequest;

class ProspectStatusResource extends RestResource
{
    public static $model = ProspectStatus::class;

    public function fields(RestRequest $request): array
    {
        return [
            'id',
            'classification',
            'name',
            'color',
            'created_at',
            'updated_at',
        ];
    }

    public function relations(RestRequest $request): array
    {
        return [
            HasMany::make('prospects', ProspectResource::class),
        ];
    }
}
