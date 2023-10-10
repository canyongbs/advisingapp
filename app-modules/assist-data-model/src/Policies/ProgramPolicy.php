<?php

namespace Assist\AssistDataModel\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\AssistDataModel\Models\Program;

class ProgramPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'program.view-any',
            denyResponse: 'You do not have permission to view programs.'
        );
    }

    public function view(User $user, Program $program): Response
    {
        return $user->canOrElse(
            abilities: ['program.*.view', "program.{$program->id}.view"],
            denyResponse: 'You do not have permission to view this program.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'program.create',
            denyResponse: 'You do not have permission to create programs.'
        );
    }

    public function update(User $user, Program $program): Response
    {
        return $user->canOrElse(
            abilities: ['program.*.update', "program.{$program->id}.update"],
            denyResponse: 'You do not have permission to update this program.'
        );
    }

    public function delete(User $user, Program $program): Response
    {
        return $user->canOrElse(
            abilities: ['program.*.delete', "program.{$program->id}.delete"],
            denyResponse: 'You do not have permission to delete this program.'
        );
    }

    public function restore(User $user, Program $program): Response
    {
        return $user->canOrElse(
            abilities: ['program.*.restore', "program.{$program->id}.restore"],
            denyResponse: 'You do not have permission to restore this program.'
        );
    }

    public function forceDelete(User $user, Program $program): Response
    {
        return $user->canOrElse(
            abilities: ['program.*.force-delete', "program.{$program->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this program.'
        );
    }
}
