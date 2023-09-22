<?php

namespace Assist\Alert\Policies;

use App\Models\User;
use Assist\Alert\Models\Alert;
use Illuminate\Auth\Access\Response;

class AlertPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('alert.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view alerts.');
    }

    public function view(User $user, Alert $alerts): Response
    {
        return $user->can('alert.*.view') || $user->can("alert.{$alerts->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this alert.');
    }

    public function create(User $user): Response
    {
        return $user->can('alert.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create alerts.');
    }

    public function update(User $user, Alert $alerts): Response
    {
        return $user->can('alert.*.update') || $user->can("alert.{$alerts->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this alert.');
    }

    public function delete(User $user, Alert $alerts): Response
    {
        return $user->can('alert.*.delete') || $user->can("alert.{$alerts->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this alert.');
    }

    public function restore(User $user, Alert $alerts): Response
    {
        return $user->can('alert.*.restore') || $user->can("alert.{$alerts->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this alert.');
    }

    public function forceDelete(User $user, Alert $alerts): Response
    {
        return $user->can('alert.*.force-delete') || $user->can("alert.{$alerts->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this alert.');
    }
}
