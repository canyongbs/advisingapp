<?php

namespace Assist\Prospect\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Prospect\Models\ProspectSource;

class ProspectSourcePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('prospect_source.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view prospect sources.');
    }

    public function view(User $user, ProspectSource $prospectSource): Response
    {
        return $user->can('prospect_source.*.view') || $user->can("prospect_source.{$prospectSource->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this prospect source.');
    }

    public function create(User $user): Response
    {
        return $user->can('prospect_source.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create prospect sources.');
    }

    public function update(User $user, ProspectSource $prospectSource): Response
    {
        return $user->can('prospect_source.*.update') || $user->can("prospect_source.{$prospectSource->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this prospect source.');
    }

    public function delete(User $user, ProspectSource $prospectSource): Response
    {
        return $user->can('prospect_source.*.delete') || $user->can("prospect_source.{$prospectSource->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this prospect source.');
    }

    public function restore(User $user, ProspectSource $prospectSource): Response
    {
        return $user->can('prospect_source.*.restore') || $user->can("prospect_source.{$prospectSource->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this prospect source.');
    }

    public function forceDelete(User $user, ProspectSource $prospectSource): Response
    {
        return $user->can('prospect_source.*.force-delete') || $user->can("prospect_source.{$prospectSource->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to force delete this prospect source.');
    }
}
