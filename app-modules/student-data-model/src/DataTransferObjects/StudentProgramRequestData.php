<?php

namespace AdvisingApp\StudentDataModel\DataTransferObjects;

use AdvisingApp\StudentDataModel\DataTransferObjects\StudentProgramData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class StudentProgramRequestData extends Data
{
    /**
     * @param DataCollection<int, StudentProgramData> $program
     */
    public function __construct(
        #[DataCollectionOf(StudentProgramData::class)]
        public DataCollection $program,
    ) {}

}