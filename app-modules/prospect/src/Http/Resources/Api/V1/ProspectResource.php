<?php

namespace AdvisingApp\Prospect\Http\Resources\Api\V1;

use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Prospect $resource
 */
class ProspectResource extends JsonResource
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
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'full_name' => $this->resource->full_name,
            'preferred' => $this->resource->preferred,
            'description' => $this->resource->description,
            'sms_opt_out' => $this->resource->sms_opt_out,
            'email_bounce' => $this->resource->email_bounce,
            'status' => $this->resource->status->name,
            'source' => $this->resource->source->name,
            'birthdate' => $this->resource->birthdate ? $this->resource->birthdate->format('Y-m-d') : null,
            'hsgrad' => $this->resource->hsgrad,
            'created_by' => $this->resource->createdBy?->name,
            'primary_email_id' => $this->resource->primary_email_id,
            'primary_phone_id' => $this->resource->primary_phone_id,
            'primary_address_id' => $this->resource->primary_address_id,
            'email_addresses' => ProspectEmailAddressResource::collection($this->whenLoaded('emailAddresses')),
            'primary_email_address' => $this->whenLoaded('primaryEmailAddress', fn (): ProspectEmailAddressResource => new ProspectEmailAddressResource($this->resource->primaryEmailAddress)),
        ];
    }
}
