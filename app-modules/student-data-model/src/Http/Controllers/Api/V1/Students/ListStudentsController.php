<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students;

use AdvisingApp\StudentDataModel\Http\Resources\Api\V1\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use Dedoc\Scramble\Attributes\Example;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class ListStudentsController
{
    /**
     * @response AnonymousResourceCollection<LengthAwarePaginator<StudentResource>>
     */
    #[Group('Students')]
    #[QueryParameter('filter[sisid]', type: 'string')]
    #[QueryParameter('filter[otherid]', type: 'string')]
    #[QueryParameter('filter[first]', type: 'string')]
    #[QueryParameter('filter[last]', type: 'string')]
    #[QueryParameter('filter[full_name]', type: 'string')]
    #[QueryParameter('filter[preferred]', type: 'string')]
    #[QueryParameter('include', type: 'string', examples: [
        'emailAddresses' => new Example('emailAddresses'),
    ])]
    #[QueryParameter('page[number]', type: 'int', default: 1)]
    #[QueryParameter('page[size]', type: 'int', default: 30)]
    #[QueryParameter('sort', type: 'string', default: 'sisid', examples: [
        'sisid' => new Example('sisid'),
        'otherid' => new Example('otherid'),
        'first' => new Example('first'),
        'last' => new Example('last'),
        'full_name' => new Example('full_name'),
        'preferred' => new Example('preferred'),
    ])]
    public function __invoke(): AnonymousResourceCollection
    {
        return QueryBuilder::for(Student::class)
            ->allowedFilters([
                'sisid',
                'otherid',
                'first',
                'last',
                'full_name',
                'preferred',
            ])
            ->allowedIncludes(['emailAddresses'])
            ->allowedSorts([
                'sisid',
                'otherid',
                'first',
                'last',
                'full_name',
                'preferred',
            ])
            ->defaultSort('sisid')
            ->jsonPaginate()
            ->toResourceCollection(StudentResource::class);
    }
}
