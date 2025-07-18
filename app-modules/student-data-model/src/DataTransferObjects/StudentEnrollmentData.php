<?php

namespace AdvisingApp\StudentDataModel\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class StudentEnrollmentData extends Data
{
    public function __construct(
        public string | Optional | null $division,
        public string | Optional | null $classNbr,
        public string | Optional | null $crseGradeOff,
        public int | Optional | null $untTaken,
        public int | Optional | null $untEarned,
        public string | Optional | null $lastUpdDtStmp,
        public string | Optional | null $section,
        public string | Optional | null $name,
        public string | Optional | null $department,
        public string | Optional | null $facultyName,
        public string | Optional | null $facultyEmail,
        public string | Optional | null $semesterCode,
        public string | Optional | null $semesterName,
        public string | Optional | null $startDate,
        public string | Optional | null $endDate,
    ) {}
}