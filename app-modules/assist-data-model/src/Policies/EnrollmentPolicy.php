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
        return $user->canOrElse(
            abilities: 'enrollment.create',
            denyResponse: 'You do not have permission to create enrollments.'
        );
    }

    public function update(User $user, Enrollment $enrollment): Response
    {
        return $user->canOrElse(
            abilities: ['enrollment.*.update', "enrollment.{$enrollment->id}.update"],
            denyResponse: 'You do not have permission to update this enrollment.'
        );
    }

    public function delete(User $user, Enrollment $enrollment): Response
    {
        return $user->canOrElse(
            abilities: ['enrollment.*.delete', "enrollment.{$enrollment->id}.delete"],
            denyResponse: 'You do not have permission to delete this enrollment.'
        );
    }

    public function restore(User $user, Enrollment $enrollment): Response
    {
        return $user->canOrElse(
            abilities: ['enrollment.*.restore', "enrollment.{$enrollment->id}.restore"],
            denyResponse: 'You do not have permission to restore this enrollment.'
        );
    }

    public function forceDelete(User $user, Enrollment $enrollment): Response
    {
        return $user->canOrElse(
            abilities: ['enrollment.*.force-delete', "enrollment.{$enrollment->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this enrollment.'
        );
    }
}
