<?php

namespace Assist\Prospect\Rest\Resources;

use Illuminate\Validation\Rule;
use Lomkit\Rest\Relations\HasMany;
use App\Rest\Resource as RestResource;
use Assist\Prospect\Models\ProspectStatus;
use Lomkit\Rest\Http\Requests\RestRequest;
use Assist\Prospect\Enums\ProspectStatusColorOptions;
use Assist\Prospect\Enums\SystemProspectClassification;

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

    public function createRules(RestRequest $request): array
    {
        return [
            'id' => ['missing'],
            'classification' => ['required', Rule::enum(SystemProspectClassification::class)],
            'name' => ['required', 'string', 'unique:prospect_statuses,name', 'max:255'],
            'color' => ['required', Rule::enum(ProspectStatusColorOptions::class)],
            'created_at' => ['missing'],
            'updated_at' => ['missing'],
        ];
    }

    public function updateRules(RestRequest $request): array
    {
        return [
            'id' => ['missing'],
            'classification' => [Rule::enum(SystemProspectClassification::class)],
            'name' => ['string', 'unique:prospect_statuses,name', 'max:255'],
            'color' => [Rule::enum(ProspectStatusColorOptions::class)],
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
