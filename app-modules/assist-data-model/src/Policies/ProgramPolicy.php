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
        return Response::deny('Programs cannot be created.');
    }

    public function update(User $user, Program $program): Response
    {
        return Response::deny('Programs cannot be updated.');
    }

    public function delete(User $user, Program $program): Response
    {
        return Response::deny('Programs cannot be deleted.');
    }

    public function restore(User $user, Program $program): Response
    {
        return Response::deny('Programs cannot be restored.');
    }

    public function forceDelete(User $user, Program $program): Response
    {
        return Response::deny('Programs cannot be force deleted.');
    }
}
