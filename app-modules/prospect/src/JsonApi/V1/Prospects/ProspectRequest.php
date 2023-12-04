<?php

namespace Assist\Prospect\JsonApi\V1\Prospects;

use LaravelJsonApi\Validation\Rule as JsonApiRule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class ProspectRequest extends ResourceRequest
{
    public function rules(): array
    {
        return [
            'firstName' => ['required', 'string'],
            'lastName' => ['required', 'string'],
            'fullName' => ['required', 'string'],
            'preferred' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'email' => ['required', 'email'],
            'email2' => ['nullable', 'email'],
            'mobile' => ['nullable', 'string'],
            'smsOptOut' => ['nullable', JsonApiRule::boolean()],
            'emailBounce' => ['nullable', JsonApiRule::boolean()],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'address2' => ['nullable', 'string'],
            'birthdate' => ['nullable', 'date_format:Y-m-d'],
            'hsgrad' => ['nullable', 'date_format:Y'],
            'status' => ['required', JsonApiRule::toOne()],
            'source' => ['required', JsonApiRule::toOne()],
            //TODO: 'assignedToId' needs User Schema
            //TODO: 'createdById' needs User Schema
        ];
    }
}
