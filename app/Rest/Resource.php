<?php

namespace App\Rest;

use Lomkit\Rest\Http\Requests\RestRequest;
use Lomkit\Rest\Http\Resource as RestResource;
use Illuminate\Contracts\Database\Eloquent\Builder;

abstract class Resource extends RestResource
{
    /**
     * Build a "search" query for fetching resource.
     *
     * @param  RestRequest  $request
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function searchQuery(RestRequest $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * Build a query for mutating resource.
     *
     * @param  RestRequest  $request
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function mutateQuery(RestRequest $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * Build a "destroy" query for the given resource.
     *
     * @param  RestRequest  $request
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function destroyQuery(RestRequest $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * Build a "restore" query for the given resource.
     *
     * @param  RestRequest  $request
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function restoreQuery(RestRequest $request, Builder $query): Builder
    {
        return $query;
    }

    /**
     * Build a "forceDelete" query for the given resource.
     *
     * @param  RestRequest  $request
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function forceDeleteQuery(RestRequest $request, Builder $query): Builder
    {
        return $query;
    }
}
