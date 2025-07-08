<?php

namespace AdvisingApp\Prospect\Actions;

use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Support\Facades\DB;

class DeleteProspect
{
    public function execute(Prospect $prospect): void
    {
        DB::transaction(function () use ($prospect) {
            // $student->enrollments()->delete();
            // $student->programs()->delete();
            // $student->alerts()->delete();
            // $student->tasks()->delete();
            // $student->interactions()->delete();
            // $student->timeline()->delete();
            // $student->formSubmissions()->delete();
            // $student->applicationSubmissions()->delete();
            // $student->eventAttendeeRecords()->delete();
            // $student->segmentSubjects()->delete();
            // $student->engagements()->delete();
            // $student->delete();

            $prospect->emailAddresses()->delete();
            $prospect->phoneNumbers()->delete();
            $prospect->addresses()->delete();
            $prospect->notes()->delete();
            $prospect->tags()->detach();
            $prospect->delete();
        });
    }
}
