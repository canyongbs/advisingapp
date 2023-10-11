<?php

namespace Assist\AssistDataModel\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\AssistDataModel\Models\Performance;

class PerformancePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'performance.view-any',
            denyResponse: 'You do not have permission to view performances.'
        );
    }

    public function view(User $user, Performance $performance): Response
    {
        return $user->canOrElse(
            abilities: ['performance.*.view', "performance.{$performance->id}.view"],
            denyResponse: 'You do not have permission to view this performance.'
        );
    }

    public function create(User $user): Response
    {
        return Response::deny('Performances cannot be created.');
    }

    public function update(User $user, Performance $performance): Response
    {
        return Response::deny('Performances cannot be updated.');
    }

    public function delete(User $user, Performance $performance): Response
    {
        return Response::deny('Performances cannot be deleted.');
    }

    public function restore(User $user, Performance $performance): Response
    {
        return Response::deny('Performances cannot be restored.');
    }

    public function forceDelete(User $user, Performance $performance): Response
    {
        return Response::deny('Performances cannot be force deleted.');
    }
}
