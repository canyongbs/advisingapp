<?php

namespace Assist\AssistDataModel\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\AssistDataModel\Models\Student;

class StudentPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'student.view-any',
            denyResponse: 'You do not have permission to view students.'
        );
    }

    public function view(User $user, Student $student): Response
    {
        return $user->canOrElse(
            abilities: ['student.*.view', "student.{$student->id}.view"],
            denyResponse: 'You do not have permission to view this student.'
        );
    }

    public function create(User $user): Response
    {
        return Response::deny('Students cannot be created.');
    }

    public function update(User $user, Student $student): Response
    {
        return Response::deny('Students cannot be updated.');
    }

    public function delete(User $user, Student $student): Response
    {
        return Response::deny('Students cannot be deleted.');
    }

    public function restore(User $user, Student $student): Response
    {
        return Response::deny('Students cannot be restored.');
    }

    public function forceDelete(User $user, Student $student): Response
    {
        return Response::deny('Students cannot be force deleted.');
    }
}
