<?php

namespace AdvisingApp\StudentDataModel\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class StudentEnrollmentData extends Data
{
    public function __construct(
        public string | Optional | null $division,
        public string | Optional | null $class_nbr,
        public string | Optional | null $crse_grade_off,
        public int | Optional | null $unt_taken,
        public int | Optional | null $unt_earned,
        public string | Optional | null $last_upd_dt_stmp,
        public string | Optional | null $section,
        public string | Optional | null $name,
        public string | Optional | null $department,
        public string | Optional | null $faculty_name,
        public string | Optional | null $faculty_email,
        public string | Optional | null $semester_code,
        public string | Optional | null $semester_name,
        public string | Optional | null $start_date,
        public string | Optional | null $end_date,
    ) {}
}