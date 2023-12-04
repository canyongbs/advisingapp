<?php

namespace Assist\Prospect\JsonApi\V1\ProspectStatuses;

use LaravelJsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use Assist\Prospect\Models\ProspectStatus;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\SoftDelete;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;

class ProspectStatusSchema extends Schema
{
    public static string $model = ProspectStatus::class;

    public function fields(): array
    {
        return [
            ID::make()->uuid(),
            Str::make('classification'),
            Str::make('name'),
            Str::make('color'),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
            SoftDelete::make('deletedAt')->sortable()->readOnly(),
            HasMany::make('prospects'),
        ];
    }

    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
        ];
    }

    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }
}
