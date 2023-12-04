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

namespace Assist\Prospect\JsonApi\V1\Prospects;

use LaravelJsonApi\Eloquent\Schema;
use Assist\Prospect\Models\Prospect;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\SoftDelete;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Filters\OnlyTrashed;
use LaravelJsonApi\Eloquent\Filters\WithTrashed;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use Assist\Prospect\JsonApi\V1\ProspectSources\ProspectSourceSchema;
use Assist\Prospect\JsonApi\V1\ProspectStatuses\ProspectStatusSchema;

class ProspectSchema extends Schema
{
    public static string $model = Prospect::class;

    protected ?array $defaultPagination = ['number' => 1];

    public function fields(): array
    {
        return [
            ID::make()->uuid(),
            Str::make('firstName'),
            Str::make('lastName'),
            Str::make('fullName'),
            Str::make('preferred'),
            Str::make('description'),
            Str::make('email'),
            Str::make('email2', 'email_2'),
            Str::make('mobile'),
            Boolean::make('smsOptOut'),
            Boolean::make('emailBounce'),
            Str::make('phone'),
            Str::make('address'),
            Str::make('address2', 'address_2'),
            DateTime::make('birthdate'),
            Str::make('hsgrad'),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
            SoftDelete::make('deletedAt')->sortable()->readOnly(),
            BelongsTo::make('status')->type(ProspectStatusSchema::type()),
            BelongsTo::make('source')->type(ProspectSourceSchema::type()),
            //TODO: 'assignedToId' needs User Schema
            //TODO: 'createdById' needs User Schema
        ];
    }

    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
            Where::make('status', 'status_id'),
            Where::make('source', 'source_id'),
            WithTrashed::make('with-trashed'),
            OnlyTrashed::make('trashed'),
        ];
    }

    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }
}
