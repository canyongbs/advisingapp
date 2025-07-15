<?php

namespace AdvisingApp\Prospect\Http\Resources\Api\V1;

use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ProspectEmailAddress $resource
 */
class ProspectEmailAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'prospect_id' => $this->resource->prospect_id,
            'address' => $this->resource->address,
            'type' => $this->resource->type,
            'order' => $this->resource->order,
        ];
    }
}
