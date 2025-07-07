<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    ...existing copyright...

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Actions;

use AdvisingApp\StudentDataModel\DataTransferObjects\UpdateStudentData;
use AdvisingApp\StudentDataModel\Models\Student;

class UpdateStudent
{
    public function execute(Student $student, UpdateStudentData $data): Student
    {
        $student->fill($data->toArray());
        $student->save();

        return $student;
    }
}
