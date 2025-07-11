<?php

namespace AdvisingApp\StudentDataModel\Actions;

use AdvisingApp\StudentDataModel\DataTransferObjects\UpdateStudentPhoneNumberData;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;

class UpdateStudentPhoneNumber
{
    public function execute(StudentPhoneNumber $studentPhoneNumber, UpdateStudentPhoneNumberData $data): StudentPhoneNumber
    {
        $studentPhoneNumber->fill($data->toArray());
        $studentPhoneNumber->save();

        return $studentPhoneNumber;
    }
}
