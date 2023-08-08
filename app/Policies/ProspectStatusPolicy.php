<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Prospect\Models\ProspectStatus;

class ProspectStatusPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('prospect_status.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view prospect statuses.');
    }

    public function view(User $user, ProspectStatus $prospectStatus): Response
    {
        return $user->can('prospect_status.*.view') || $user->can("prospect_status.{$prospectStatus->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view prospect statuses.');
    }

    public function create(User $user): Response
    {
        return $user->can('prospect_status.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create prospect statuses.');
    }

    public function update(User $user, ProspectStatus $prospectStatus): Response
    {
        return $user->can('prospect_status.*.update') || $user->can("prospect_status.{$prospectStatus->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update prospect statuses.');
    }

    public function delete(User $user, ProspectStatus $prospectStatus): Response
    {
        return $user->can('prospect_status.*.delete') || $user->can("prospect_status.{$prospectStatus->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete prospect statuses.');
    }

    public function restore(User $user, ProspectStatus $prospectStatus): Response
    {
        return $user->can('prospect_status.*.restore') || $user->can("prospect_status.{$prospectStatus->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore prospect statuses.');
    }

    public function forceDelete(User $user, ProspectStatus $prospectStatus): Response
    {
        return $user->can('prospect_status.*.force-delete') || $user->can("prospect_status.{$prospectStatus->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to force delete prospect statuses.');
    }
}
