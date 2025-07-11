<?php

namespace AdvisingApp\StudentDataModel\Http\Resources\Api\V1;

use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property StudentPhoneNumber $resource
 */
class StudentPhoneNumberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'sisid' => $this->resource->sisid,
            'number' => $this->resource->number,
            'type' => $this->resource->type,
            'order' => $this->resource->order,
            'ext' => $this->resource->ext,
            'can_receive_sms' => $this->resource->can_receive_sms,
        ];
    }
}
