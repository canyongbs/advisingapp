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

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students;

use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListStudentsController
{
    /**
     * @response AnonymousResourceCollection<LengthAwarePaginator<StudentResource>>
     */
    #[Group('Students')]
    #[QueryParameter('filter[sisid]', description: 'Filter the results where the student\'s SISID contains the provided string.', type: 'string')]
    #[QueryParameter('filter[otherid]', description: 'Filter the results where the student\'s OTHERID contains the provided string.', type: 'string')]
    #[QueryParameter('filter[first]', description: 'Filter the results where the student\'s first name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[last]', description: 'Filter the results where the student\'s last name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[full_name]', description: 'Filter the results where the student\'s full name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[preferred]', description: 'Filter the results where the student\'s preferred name contains the provided string.', type: 'string')]
    #[QueryParameter('filter[gender]', description: 'Filter the results where the student\'s gender contains the provided string.', type: 'string')]
    #[QueryParameter('filter[ethnicity]', description: 'Filter the results where the student\'s ethnicity contains the provided string.', type: 'string')]
    #[QueryParameter('filter[emailAddress]', description: 'Filter the results where any of the student\'s email addresses contains the provided string.', type: 'string')]
    #[QueryParameter('filter[primaryEmailAddress]', description: 'Filter the results where the student\'s primary email address contains the provided string.', type: 'string')]
    #[QueryParameter('filter[birthdate]', description: 'Filter the results where the student\'s birthdate matches the provided date.', type: 'date')]
    #[QueryParameter('filter[dfw]', description: 'Filter the results where the student\'s DFW date matches the provided date.', type: 'date')]
    #[QueryParameter('filter[lastlmslogin]', description: 'Filter the results where the student\'s last LMS login matches the provided date.', type: 'date')]
    #[QueryParameter('filter[created_at_source]', description: 'Filter the results where the student\'s created_at_source matches the provided datetime.', type: 'datetime')]
    #[QueryParameter('filter[updated_at_source]', description: 'Filter the results where the student\'s updated_at_source matches the provided datetime.', type: 'datetime')]
    #[QueryParameter('filter[hsgrad]', description: 'Filter the results where the student\'s high school graduation date matches the provided date.', type: 'date')]
    #[QueryParameter('filter[holds]', description: 'Filter the results where the student\'s holds matches the provided integer.', type: 'integer')]
    #[QueryParameter('filter[dual]', description: 'Filter the results where the student\'s dual matches the provided boolean.', type: 'boolean')]
    #[QueryParameter('filter[ferpa]', description: 'Filter the results where the student\'s ferpa matches the provided boolean.', type: 'boolean')]
    #[QueryParameter('filter[sap]', description: 'Filter the results where the student\'s sap matches the provided boolean.', type: 'boolean')]
    #[QueryParameter('filter[firstgen]', description: 'Filter the results where the student\'s firstgen matches the provided boolean.', type: 'boolean')]
    #[QueryParameter('filter[primary_email_id]', description: 'Filter the results where the student\'s primary_email_id matches the provided integer.', type: 'integer')]
    #[QueryParameter('filter[primary_phone_id]', description: 'Filter the results where the student\'s primary_phone_id matches the provided integer.', type: 'integer')]
    #[QueryParameter('filter[primary_address_id]', description: 'Filter the results where the student\'s primary_address_id matches the provided integer.', type: 'integer')]
    #[QueryParameter('include', description: 'Include related resources in the response.', type: 'string', examples: [
        'email_addresses' => new Example('email_addresses'),
        'primary_email_address' => new Example('primary_email_address'),
        'phone_numbers' => new Example('phone_numbers'),
        'primary_phone_number' => new Example('primary_phone_number'),
        'first_enrollment_term' => new Example('first_enrollment_term'),
        'most_recent_enrollment_term' => new Example('most_recent_enrollment_term'),
    ])]
    #[QueryParameter('page[number]', description: 'Control which page of students is returned in the response.', type: 'int', default: 1)]
    #[QueryParameter('page[size]', description: 'Control how many students are returned in the response.', type: 'int', default: 30)]
    #[QueryParameter('sort', description: 'Control the order of students that are returned in the response. Ascending order is used by default, prepend the sort with `-` to sort descending.', type: 'string', default: 'sisid', examples: [
        'sisid' => new Example('sisid'),
        'otherid' => new Example('otherid'),
        'first' => new Example('first'),
        'last' => new Example('last'),
        'full_name' => new Example('full_name'),
        'preferred' => new Example('preferred'),
        'gender' => new Example('gender'),
        'ethnicity' => new Example('ethnicity'),
        'birthdate' => new Example('birthdate'),
        'dfw' => new Example('dfw'),
        'lastlmslogin' => new Example('lastlmslogin'),
        'created_at_source' => new Example('created_at_source'),
        'updated_at_source' => new Example('updated_at_source'),
        'hsgrad' => new Example('hsgrad'),
        'holds' => new Example('holds'),
        'primary_email_id' => new Example('primary_email_id'),
        'primary_phone_id' => new Example('primary_phone_id'),
        'primary_address_id' => new Example('primary_address_id'),
    ])]
    public function __invoke(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Student::class);

        return QueryBuilder::for(Student::class)
            ->allowedFilters([
                AllowedFilter::partial('sisid'),
                AllowedFilter::partial('otherid'),
                AllowedFilter::partial('first'),
                AllowedFilter::partial('last'),
                AllowedFilter::partial('full_name'),
                AllowedFilter::partial('preferred'),
                AllowedFilter::partial('gender'),
                AllowedFilter::partial('ethnicity'),
                AllowedFilter::partial('emailAddress', 'emailAddresses.address'),
                AllowedFilter::partial('primaryEmailAddress', 'primaryEmailAddress.address'),
                AllowedFilter::exact('birthdate'),
                AllowedFilter::exact('dfw'),
                AllowedFilter::exact('lastlmslogin'),
                AllowedFilter::exact('created_at_source'),
                AllowedFilter::exact('updated_at_source'),
                AllowedFilter::exact('hsgrad'),
                AllowedFilter::exact('holds'),
                AllowedFilter::exact('dual'),
                AllowedFilter::exact('ferpa'),
                AllowedFilter::exact('sap'),
                AllowedFilter::exact('firstgen'),
                AllowedFilter::exact('primary_email_id'),
                AllowedFilter::exact('primary_phone_id'),
                AllowedFilter::exact('primary_address_id'),
            ])
            ->allowedIncludes([
                AllowedInclude::relationship('email_addresses', 'emailAddresses'),
                AllowedInclude::relationship('primary_email_address', 'primaryEmailAddress'),
                AllowedInclude::relationship('phone_numbers', 'phoneNumbers'),
                AllowedInclude::relationship('primary_phone_number', 'primaryPhoneNumber'),
                AllowedInclude::relationship('first_enrollment_term', 'firstEnrollmentTerm'),
                AllowedInclude::relationship('most_recent_enrollment_term', 'mostRecentEnrollmentTerm'),
            ])
            ->allowedSorts([
                AllowedSort::field('sisid'),
                AllowedSort::field('otherid'),
                AllowedSort::field('first'),
                AllowedSort::field('last'),
                AllowedSort::field('full_name'),
                AllowedSort::field('preferred'),
                AllowedSort::field('gender'),
                AllowedSort::field('ethnicity'),
                AllowedSort::field('birthdate'),
                AllowedSort::field('dfw'),
                AllowedSort::field('lastlmslogin'),
                AllowedSort::field('created_at_source'),
                AllowedSort::field('updated_at_source'),
                AllowedSort::field('hsgrad'),
                AllowedSort::field('holds'),
                AllowedSort::field('primary_email_id'),
                AllowedSort::field('primary_phone_id'),
                AllowedSort::field('primary_address_id'),
            ])
            ->defaultSort(AllowedSort::field('sisid'))
            ->jsonPaginate()
            ->toResourceCollection(StudentResource::class);
    }
}
