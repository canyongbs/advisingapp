<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

    public function createRules(RestRequest $request): array
    {
        return [
            'id' => ['missing'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'preferred' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'email' => ['nullable', 'email', 'max:255'],
            'email_2' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'sms_opt_out' => ['boolean'],
            'email_bounce' => ['boolean'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'hsgrad' => ['nullable', 'integer', 'digits:4'],
            'created_at' => ['missing'],
            'updated_at' => ['missing'],
        ];
    }

    public function updateRules(RestRequest $request): array
    {
        return [
            'id' => ['missing'],
            'first_name' => ['string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'full_name' => ['string', 'max:255'],
            'preferred' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'email' => ['nullable', 'email', 'max:255'],
            'email_2' => ['nullable', 'email', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'sms_opt_out' => ['boolean'],
            'email_bounce' => ['boolean'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_2' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'hsgrad' => ['nullable', 'integer', 'digits:4'],
            'created_at' => ['missing'],
            'updated_at' => ['missing'],
        ];
    }

    public function relations(RestRequest $request): array
    {
        return [
            BelongsTo::make('status', ProspectStatusResource::class)->requiredOnCreation(),
            BelongsTo::make('source', ProspectSourceResource::class)->requiredOnCreation(),
        ];
    }

    public function scopes(RestRequest $request): array
    {
        return [];
    }
}
