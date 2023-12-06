<?php

namespace Assist\Prospect\Rest\Resources;

use Assist\Prospect\Models\Prospect;
use App\Rest\Resource as RestResource;
use Lomkit\Rest\Http\Requests\RestRequest;

class ProspectResource extends RestResource
{
    public static $model = Prospect::class;

    public function fields(RestRequest $request): array
    {
        return [
            'id',
        ];
    }

    /**
     * The exposed relations that could be provided
     *
     * @param RestRequest $request
     *
     * @return array
     */
    public function relations(RestRequest $request): array
    {
        return [];
    }

    /**
     * The exposed scopes that could be provided
     *
     * @param RestRequest $request
     *
     * @return array
     */
    public function scopes(RestRequest $request): array
    {
        return [];
    }
}
