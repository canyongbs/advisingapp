<?php

namespace Assist\Prospect\JsonApi\V1\ProspectSources;

use Assist\Prospect\Models\ProspectSource;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\SoftDelete;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class ProspectSourceSchema extends Schema
{
    public static string $model = ProspectSource::class;

    public function fields(): array
    {
        return [
            ID::make()->uuid(),
            Str::make('name'),
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
