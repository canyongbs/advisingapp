<?php

namespace AdvisingApp\StudentDataModel\DataTransferObjects;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class StudentEnrollmentRequestData extends Data
{
    /**
     * @param DataCollection<int, StudentEnrollmentData> $enrollments
     */
    public function __construct(
        #[DataCollectionOf(StudentEnrollmentData::class)]
        public DataCollection $enrollments,
    ) {}
}