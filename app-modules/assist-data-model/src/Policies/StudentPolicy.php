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
        return $user->canOrElse(
            abilities: 'student.create',
            denyResponse: 'You do not have permission to create students.'
        );
    }

    public function update(User $user, Student $student): Response
    {
        return $user->canOrElse(
            abilities: ['student.*.update', "student.{$student->id}.update"],
            denyResponse: 'You do not have permission to update this student.'
        );
    }

    public function delete(User $user, Student $student): Response
    {
        return $user->canOrElse(
            abilities: ['student.*.delete', "student.{$student->id}.delete"],
            denyResponse: 'You do not have permission to delete this student.'
        );
    }

    public function restore(User $user, Student $student): Response
    {
        return $user->canOrElse(
            abilities: ['student.*.restore', "student.{$student->id}.restore"],
            denyResponse: 'You do not have permission to restore this student.'
        );
    }

    public function forceDelete(User $user, Student $student): Response
    {
        return $user->canOrElse(
            abilities: ['student.*.force-delete', "student.{$student->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this student.'
        );
    }
}
