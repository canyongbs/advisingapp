<?php

namespace AdvisingApp\EnrollmentRecordManager\Policies;

use AdvisingApp\EnrollmentRecordManager\Models\ManageableEnrollment;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ManageableEnrollmentPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'student_record_manager.view-any',
            denyResponse: 'You do not have permission to view manageable program.'
        );
    }

    public function view(Authenticatable $authenticatable, ManageableEnrollment $manageableEnrollment): Response
    {
        return $authenticatable->canOrElse(
            abilities: "student_record_manager.{$manageableEnrollment->id}.view-any",
            denyResponse: 'You do not have permission to view manageable program.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'student_record_manager.create',
            denyResponse: 'You do not have permission to create manageable program.'
        );
    }

    public function update(Authenticatable $authenticatable, ManageableEnrollment $manageableEnrollment): Response
    {
        return $authenticatable->canOrElse(
            abilities: "student_record_manager.{$manageableEnrollment->id}.update",
            denyResponse: 'You do not have permission to update manageable program.'
        );
    }

    public function delete(Authenticatable $authenticatable, ManageableEnrollment $manageableEnrollment): Response
    {
        return $authenticatable->canOrElse(
            abilities: "student_record_manager.{$manageableEnrollment->id}.delete",
            denyResponse: 'You do not have permission to delete manageable program.'
        );
    }

    public function restore(Authenticatable $authenticatable, ManageableEnrollment $manageableEnrollment): Response
    {
        return Response::deny('Manageable programs cannot be restored.');
    }

    public function forceDelete(Authenticatable $authenticatable, ManageableEnrollment $manageableEnrollment): Response
    {
        return Response::deny('Manageable programs cannot be force deleted.');
    }
}
