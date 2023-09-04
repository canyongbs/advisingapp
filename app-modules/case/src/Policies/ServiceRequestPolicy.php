<?php

namespace Assist\Case\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Case\Models\ServiceRequest;

class ServiceRequestPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('case_item.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view case items.');
    }

    public function view(User $user, ServiceRequest $caseItem): Response
    {
        return $user->can('case_item.*.view') || $user->can("case_item.{$caseItem->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this case item.');
    }

    public function create(User $user): Response
    {
        return $user->can('case_item.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create case items.');
    }

    public function update(User $user, ServiceRequest $caseItem): Response
    {
        return $user->can('case_item.*.update') || $user->can("case_item.{$caseItem->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this case item.');
    }

    public function delete(User $user, ServiceRequest $caseItem): Response
    {
        return $user->can('case_item.*.delete') || $user->can("case_item.{$caseItem->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this case item.');
    }

    public function restore(User $user, ServiceRequest $caseItem): Response
    {
        return $user->can('case_item.*.restore') || $user->can("case_item.{$caseItem->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this case item.');
    }

    public function forceDelete(User $user, ServiceRequest $caseItem): Response
    {
        return $user->can('case_item.*.force-delete') || $user->can("case_item.{$caseItem->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this case item.');
    }
}
