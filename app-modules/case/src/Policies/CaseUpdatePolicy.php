<?php

namespace Assist\Case\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Case\Models\ServiceRequestUpdate;

class CaseUpdatePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('case_update.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view case updates.');
    }

    public function view(User $user, ServiceRequestUpdate $caseUpdate): Response
    {
        return $user->can('case_update.*.view') || $user->can("case_update.{$caseUpdate->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this case update.');
    }

    public function create(User $user): Response
    {
        return $user->can('case_update.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create case updates.');
    }

    public function update(User $user, ServiceRequestUpdate $caseUpdate): Response
    {
        return $user->can('case_update.*.update') || $user->can("case_update.{$caseUpdate->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this case update.');
    }

    public function delete(User $user, ServiceRequestUpdate $caseUpdate): Response
    {
        return $user->can('case_update.*.delete') || $user->can("case_update.{$caseUpdate->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this case update.');
    }

    public function restore(User $user, ServiceRequestUpdate $caseUpdate): Response
    {
        return $user->can('case_update.*.restore') || $user->can("case_update.{$caseUpdate->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this case update.');
    }

    public function forceDelete(User $user, ServiceRequestUpdate $caseUpdate): Response
    {
        return $user->can('case_update.*.force-delete') || $user->can("case_update.{$caseUpdate->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to force delete this case update.');
    }
}
