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

namespace AdvisingApp\Prospect\Http\Controllers\Api\V1\Prospects;

use AdvisingApp\Prospect\Http\Resources\Api\V1\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Sorts\ProspectSourceSort;
use AdvisingApp\Prospect\Sorts\ProspectStatusSort;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class ListProspectsController
{
    /**
     * @response AnonymousResourceCollection<LengthAwarePaginator<ProspectResource>>
     */
    #[Group('Prospects')]
    #[QueryParameter('filter[id]', description: 'Filter the results where the prospect\'s ID contains the provided string.', type: 'string')]
    #[QueryParameter('filter[first_name]', description: 'Filter the results where the prospect\'s first name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[last_name]', description: 'Filter the results where the prospect\'s last name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[full_name]', description: 'Filter the results where the prospect\'s full name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[preferred]', description: 'Filter the results where the prospect\'s preferred name contains the provided string (case-insensitive, partial match).', type: 'string')]
    #[QueryParameter('filter[birthdate]', description: 'Filter the results where the prospect\'s birthdate matches the provided date using comparison operators. Supported: =, <, <=, >, >=. Format: YYYY-MM-DD.', type: 'date')]
    #[QueryParameter('filter[status]', description: 'Filter the results where the prospect\'s status name (case-insensitive) matches the provided string. Example: filter[status]=converted.', type: 'string')]
    #[QueryParameter('filter[source]', description: 'Filter the results where the prospect\'s source name (case-insensitive) matches the provided string. Example: filter[source]=import.', type: 'string')]
    #[QueryParameter('filter[hsgrad]', description: 'Filter the results where the prospect\'s high school graduation year matches the provided integer using comparison operators. Supported operators: =, <, <=, >, >=.', type: 'integer')]
    #[QueryParameter('filter[primary_email_id]', description: 'Filter the results where the prospect\'s primary_email_id matches the provided integer.', type: 'integer')]
    #[QueryParameter('filter[primary_phone_id]', description: 'Filter the results where the prospect\'s primary_phone_id matches the provided integer.', type: 'integer')]
    #[QueryParameter('filter[primary_address_id]', description: 'Filter the results where the prospect\'s primary_address_id matches the provided integer.', type: 'integer')]
    #[QueryParameter('filter[emailAddress]', description: 'Filter the results where any of the prospect\'s email addresses contains the provided string.', type: 'string')]
    #[QueryParameter('filter[primaryEmailAddress]', description: 'Filter the results where the prospect\'s primary email address contains the provided string.', type: 'string')]
    #[QueryParameter('include', description: 'Include related resources in the response.', type: 'string', examples: [
        'email_addresses' => new Example('email_addresses'),
        'primary_email_address' => new Example('primary_email_address'),
    ])]
    #[QueryParameter('page[number]', description: 'Control which page of prospects is returned in the response.', type: 'int', default: 1)]
    #[QueryParameter('page[size]', description: 'Control how many prospects are returned in the response.', type: 'int', default: 30)]
    #[QueryParameter('sort', description: 'Control the order of prospects that are returned in the response. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'id', examples: [
        'id' => new Example('id'),
        'first_name' => new Example('first_name'),
        'last_name' => new Example('last_name'),
        'full_name' => new Example('full_name'),
        'preferred' => new Example('preferred'),
        'birthdate' => new Example('birthdate'),
        'hsgrad' => new Example('hsgrad'),
        'status' => new Example('status'),
        'source' => new Example('source'),
        'primary_email_id' => new Example('primary_email_id'),
        'primary_phone_id' => new Example('primary_phone_id'),
        'primary_address_id' => new Example('primary_address_id'),
    ])]
    public function __invoke(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Prospect::class);

        return QueryBuilder::for(Prospect::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('first_name'),
                AllowedFilter::partial('last_name'),
                AllowedFilter::partial('full_name'),
                AllowedFilter::partial('preferred'),
                AllowedFilter::operator('birthdate', FilterOperator::DYNAMIC),
                AllowedFilter::operator('hsgrad', FilterOperator::DYNAMIC),
                AllowedFilter::callback('status', function (Builder $query, mixed $value): Builder {
                    return $query->whereHas('status', function (Builder $query) use ($value) {
                        $query->whereRaw('LOWER(name) = ?', [Str::lower($value)]);
                    });
                }),
                AllowedFilter::callback('source', function (Builder $query, mixed $value): Builder {
                    return $query->whereHas('source', function (Builder $query) use ($value) {
                        $query->whereRaw('LOWER(name) = ?', [Str::lower($value)]);
                    });
                }),
                AllowedFilter::exact('primary_email_id'),
                AllowedFilter::exact('primary_phone_id'),
                AllowedFilter::exact('primary_address_id'),
                AllowedFilter::partial('emailAddress', 'emailAddresses.address'),
                AllowedFilter::partial('primaryEmailAddress', 'primaryEmailAddress.address'),
            ])
            ->allowedIncludes([
                AllowedInclude::relationship('email_addresses', 'emailAddresses'),
                AllowedInclude::relationship('primary_email_address', 'primaryEmailAddress'),
            ])
            ->allowedSorts([
                AllowedSort::field('id'),
                AllowedSort::field('first_name'),
                AllowedSort::field('last_name'),
                AllowedSort::field('full_name'),
                AllowedSort::field('preferred'),
                AllowedSort::field('birthdate'),
                AllowedSort::field('hsgrad'),
                AllowedSort::custom('status', new ProspectStatusSort(), 'name'),
                AllowedSort::custom('source', new ProspectSourceSort(), 'name'),
                AllowedSort::field('primary_email_id'),
                AllowedSort::field('primary_phone_id'),
                AllowedSort::field('primary_address_id'),
            ])
            ->defaultSort(AllowedSort::field('id'))
            ->jsonPaginate()
            ->toResourceCollection(ProspectResource::class);
    }
}
