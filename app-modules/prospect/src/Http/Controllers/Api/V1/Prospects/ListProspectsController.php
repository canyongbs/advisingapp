<?php

namespace AdvisingApp\Prospect\Http\Controllers\Api\V1\Prospects;

use AdvisingApp\Prospect\Http\Resources\Api\V1\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
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
    #[QueryParameter('filter[preferred]', description: 'Filter the results where the prospect\'s preferred name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[description]', description: 'Filter the results where the prospect\'s description contains the provided string.', type: 'string')]
    #[QueryParameter('filter[sms_opt_out]', description: 'Filter the results where the prospect\'s sms_opt_out matches the provided boolean.', type: 'boolean')]
    #[QueryParameter('filter[email_bounce]', description: 'Filter the results where the prospect\'s email_bounce matches the provided boolean.', type: 'boolean')]
    #[QueryParameter('filter[birthdate]', description: 'Filter the results where the prospect\'s birthdate matches the provided date.', type: 'date')]
    #[QueryParameter('filter[hsgrad]', description: 'Filter the results where the prospect\'s high school graduation year matches the provided integer.', type: 'integer')]
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
    #[QueryParameter('sort', description: 'Control the order of prospects that are returned in the response. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'sisid', examples: [
        'id' => new Example('id'),
        'first_name' => new Example('first_name'),
        'last_name' => new Example('last_name'),
        'full_name' => new Example('full_name'),
        'preferred' => new Example('preferred'),
        'birthdate' => new Example('birthdate'),
        'hsgrad' => new Example('hsgrad'),
        'primary_email_id' => new Example('primary_email_id'),
        'primary_phone_id' => new Example('primary_phone_id'),
        'primary_address_id' => new Example('primary_address_id'),
    ])]
    public function __invoke(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Prospect::class);

        return QueryBuilder::for(Prospect::class)
            ->allowedFilters([
                AllowedFilter::partial('id'),
                AllowedFilter::partial('first_name'),
                AllowedFilter::partial('last_name'),
                AllowedFilter::partial('full_name'),
                AllowedFilter::partial('preferred'),
                AllowedFilter::exact('sms_opt_out'),
                AllowedFilter::exact('email_bounce'),
                AllowedFilter::operator('birthdate', FilterOperator::DYNAMIC),
                AllowedFilter::exact('hsgrad'),
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
                AllowedSort::field('primary_email_id'),
                AllowedSort::field('primary_phone_id'),
                AllowedSort::field('primary_address_id'),
            ])
            ->defaultSort(AllowedSort::field('id'))
            ->jsonPaginate()
            ->toResourceCollection(ProspectResource::class);
    }
}
