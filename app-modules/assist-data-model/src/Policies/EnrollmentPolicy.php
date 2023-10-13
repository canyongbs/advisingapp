<?php

namespace Assist\AssistDataModel\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\AssistDataModel\Models\Enrollment;

class EnrollmentPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'enrollment.view-any',
            denyResponse: 'You do not have permission to view enrollments.'
        );
    }

    public function view(User $user, Enrollment $enrollment): Response
    {
        return $user->canOrElse(
            abilities: ['enrollment.*.view', "enrollment.{$enrollment->id}.view"],
            denyResponse: 'You do not have permission to view this enrollment.'
        );
    }

    public function create(User $user): Response
    {
        return Response::deny('Enrollments cannot be created.');
    }

    public function update(User $user, Enrollment $enrollment): Response
    {
        return Response::deny('Enrollments cannot be updated.');
    }

    public function delete(User $user, Enrollment $enrollment): Response
    {
        return Response::deny('Enrollments cannot be deleted.');
    }

    public function restore(User $user, Enrollment $enrollment): Response
    {
        return Response::deny('Enrollments cannot be restored.');
    }

    public function forceDelete(User $user, Enrollment $enrollment): Response
    {
        return Response::deny('Enrollments cannot be force deleted.');
    }
}
