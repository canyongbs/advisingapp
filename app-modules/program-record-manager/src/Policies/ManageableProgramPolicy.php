<?php

namespace AdvisingApp\ProgramRecordManager\Policies;

use AdvisingApp\ProgramRecordManager\Models\ManageableProgram;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ManageableProgramPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'student_record_manager.view-any',
            denyResponse: 'You do not have permission to view manageable program.'
        );
    }

    public function view(Authenticatable $authenticatable, ManageableProgram $manageableProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: "student_record_manager.{$manageableProgram->id}.view-any",
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

    public function update(Authenticatable $authenticatable, ManageableProgram $manageableProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: "student_record_manager.{$manageableProgram->id}.update",
            denyResponse: 'You do not have permission to update manageable program.'
        );
    }

    public function delete(Authenticatable $authenticatable, ManageableProgram $manageableProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: "student_record_manager.{$manageableProgram->id}.delete",
            denyResponse: 'You do not have permission to delete manageable program.'
        );
    }

    public function restore(Authenticatable $authenticatable, ManageableProgram $manageableProgram): Response
    {
        return Response::deny('Manageable programs cannot be restored.');
    }

    public function forceDelete(Authenticatable $authenticatable, ManageableProgram $manageableProgram): Response
    {
        return Response::deny('Manageable programs cannot be force deleted.');
    }
}
