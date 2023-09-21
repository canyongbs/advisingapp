<?php

namespace Assist\Prospect\Policies;

use App\Models\User;
use Assist\Prospect\Models\Prospect;
use Illuminate\Auth\Access\Response;

class ProspectPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('prospect.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view prospects.');
    }

    public function view(User $user, Prospect $prospect): Response
    {
        return $user->can('prospect.*.view') || $user->can("prospect.{$prospect->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this prospect.');
    }

    public function create(User $user): Response
    {
        return $user->can('prospect.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create prospects.');
    }

    public function import(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect.import',
            denyResponse: 'You do not have permission to import prospects.',
        );
    }

    public function update(User $user, Prospect $prospect): Response
    {
        return $user->can('prospect.*.update') || $user->can("prospect.{$prospect->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this prospect.');
    }

    public function delete(User $user, Prospect $prospect): Response
    {
        return $user->can('prospect.*.delete') || $user->can("prospect.{$prospect->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this prospect.');
    }

    public function restore(User $user, Prospect $prospect): Response
    {
        return $user->can('prospect.*.restore') || $user->can("prospect.{$prospect->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this prospect.');
    }

    public function forceDelete(User $user, Prospect $prospect): Response
    {
        return $user->can('prospect.*.force-delete') || $user->can("prospect.{$prospect->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to force delete this prospect.');
    }
}
