<?php

namespace AdvisingApp\StudentRecordManager\Observers;

use AdvisingApp\StudentRecordManager\Models\ManageableStudent;

class ManageStudentObserver
{
    /**
     * Handle the ManageableStudent "created" event.
     */
    public function created(ManageableStudent $manageableStudent): void
    {
        //
    }

    /**
     * Handle the ManageableStudent "updated" event.
     */
    public function updated(ManageableStudent $manageableStudent): void
    {
        //
    }

    /**
     * Handle the ManageableStudent "deleted" event.
     */
    public function deleted(ManageableStudent $manageableStudent): void
    {
        //
    }

    /**
     * Handle the ManageableStudent "trashed" event.
     */
    public function trashed(ManageableStudent $manageableStudent): void
    {
        // Soft delete related models when student is trashed
        foreach ($manageableStudent->enrollments as $enrollment) {
            $enrollment->delete(); // Soft delete each related enrollment
        }

        foreach ($manageableStudent->courses as $course) {
            $course->delete(); // Soft delete each related course
        }

        foreach ($manageableStudent->grades as $grade) {
            $grade->delete(); // Soft delete each related grade
        }
    }

    /**
     * Handle the ManageableStudent "restored" event.
     */
    public function restored(ManageableStudent $manageableStudent): void
    {
        //
    }

    /**
     * Handle the ManageableStudent "force deleted" event.
     */
    public function forceDeleted(ManageableStudent $manageableStudent): void
    {
        //
    }
}
