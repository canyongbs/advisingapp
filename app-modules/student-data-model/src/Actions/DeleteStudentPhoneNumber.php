<?php

namespace AdvisingApp\StudentDataModel\Actions;

use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Illuminate\Support\Facades\DB;

class DeleteStudentPhoneNumber
{
    public function execute(StudentPhoneNumber $studentPhoneNumber): void
    {
        DB::transaction(function () use ($studentPhoneNumber) {
            if ($studentPhoneNumber->student?->primaryPhoneNumber()->is($studentPhoneNumber)) {
                $studentPhoneNumber->student->primaryPhoneNumber()->associate(
                    $studentPhoneNumber->student->phoneNumbers()->whereKeyNot($studentPhoneNumber)->first(),
                );
                $studentPhoneNumber->student->save();
            }

            $studentPhoneNumber->delete();
        });
    }
}
