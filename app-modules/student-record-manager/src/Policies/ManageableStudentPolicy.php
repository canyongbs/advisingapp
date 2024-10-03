<?php

namespace AdvisingApp\StudentRecordManager\Policies;

use AdvisingApp\StudentRecordManager\Models\ManageableStudent;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class ManageableStudentPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'student_record_manager.view-any',
            denyResponse: 'You do not have permission to view manageable student.'
        );
    }

    public function view(Authenticatable $authenticatable, ManageableStudent $manageableStudent): Response
    {
        return $authenticatable->canOrElse(
            abilities: "student_record_manager.{$manageableStudent->id}.view-any",
            denyResponse: 'You do not have permission to view manageable student.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'student_record_manager.create',
            denyResponse: 'You do not have permission to create manageable student.'
        );
    }

    public function update(Authenticatable $authenticatable, ManageableStudent $manageableStudent): Response
    {
        return Response::deny('Manageable Students cannot be deleted.');
    }

    public function delete(Authenticatable $authenticatable, ManageableStudent $manageableStudent): Response
    {
        return Response::deny('Manageable Students cannot be deleted.');
    }

    public function restore(Authenticatable $authenticatable, ManageableStudent $manageableStudent): Response
    {
        return Response::deny('Manageable Students cannot be deleted.');
    }

    public function forceDelete(Authenticatable $authenticatable, ManageableStudent $manageableStudent): Response
    {
        return Response::deny('Manageable Students cannot be deleted.');
    }
}
