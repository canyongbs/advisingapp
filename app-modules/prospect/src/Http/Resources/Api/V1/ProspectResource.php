<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
            'id' => $this->resource->getKey(),
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'full_name' => $this->resource->full_name,
            'preferred' => $this->resource->preferred,
            'description' => $this->resource->description,
            'status' => $this->resource->status->name,
            'source' => $this->resource->source->name,
            'birthdate' => $this->resource->birthdate ? $this->resource->birthdate->format('Y-m-d') : null,
            'hsgrad' => $this->resource->hsgrad,
            'primary_email_id' => $this->resource->primary_email_id,
            'primary_phone_id' => $this->resource->primary_phone_id,
            'primary_address_id' => $this->resource->primary_address_id,
            'email_addresses' => ProspectEmailAddressResource::collection($this->whenLoaded('emailAddresses')),
            'primary_email_address' => $this->whenLoaded('primaryEmailAddress', fn (): ProspectEmailAddressResource => new ProspectEmailAddressResource($this->resource->primaryEmailAddress)),
        ];
    }
}
