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
use LaravelJsonApi\Eloquent\Resources\Relation;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;

class ProspectSchema extends Schema
{
    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Prospect::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make()->uuid(),
            Str::make('first_name'),
            Str::make('last_name'),
            Str::make('full_name'),
            Str::make('preferred'),
            Str::make('description'),
            Str::make('email'),
            Str::make('email_2'),
            Str::make('mobile'),
            Boolean::make('sms_opt_out'),
            Boolean::make('email_bounce'),
            Str::make('phone'),
            Str::make('address'),
            Str::make('address_2'),
            DateTime::make('birthdate'),
            Str::make('hsgrad'),
            DateTime::make('created_at')->sortable()->readOnly(),
            DateTime::make('updated_at')->sortable()->readOnly(),
            SoftDelete::make('deleted_at')->sortable()->readOnly(),
            BelongsTo::make('status', 'status')->type(ProspectStatusSchema::type()),
                // ->serializeUsing(fn (Relation $relation) => $relation->alwaysShowData()),
            // Str::make('classification')->on('status'),
        ];
    }

    /**
     * Get the resource filters.
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
            Where::make('status', 'status_id'),
        ];
    }

    /**
     * Get the resource paginator.
     *
     * @return Paginator|null
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }
}
