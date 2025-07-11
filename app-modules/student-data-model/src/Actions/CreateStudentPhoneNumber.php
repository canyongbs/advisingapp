<?php

namespace AdvisingApp\StudentDataModel\Actions;

use AdvisingApp\StudentDataModel\DataTransferObjects\CreateStudentPhoneNumberData;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Illuminate\Support\Facades\DB;

class CreateStudentPhoneNumber
{
    public function execute(Student $student, CreateStudentPhoneNumberData $data): StudentPhoneNumber
    {
        return DB::transaction(function () use ($data, $student) {
            $phoneNumber = new StudentPhoneNumber();
            $phoneNumber->student()->associate($student);
            $phoneNumber->fill($data->toArray());
            $phoneNumber->save();

            if (! $student->primaryPhoneNumber()->exists()) {
                $student->primaryPhoneNumber()->associate($student->phoneNumbers->first());
                $student->save();
            }

            return $phoneNumber;
        });
    }
}
