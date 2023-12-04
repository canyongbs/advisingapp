<?php

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
