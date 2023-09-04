<?php

namespace Assist\Case\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Case\Models\ServiceRequestPriority;

class CaseItemPriorityPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('case_item_priority.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view case item priorities.');
    }

    public function view(User $user, ServiceRequestPriority $caseItemPriority): Response
    {
        return $user->can('case_item_priority.*.view') || $user->can("case_item_priority.{$caseItemPriority->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this case item priority.');
    }

    public function create(User $user): Response
    {
        return $user->can('case_item_priority.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create case item priorities.');
    }

    public function update(User $user, ServiceRequestPriority $caseItemPriority): Response
    {
        return $user->can('case_item_priority.*.update') || $user->can("case_item_priority.{$caseItemPriority->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this case item priority.');
    }

    public function delete(User $user, ServiceRequestPriority $caseItemPriority): Response
    {
        return $user->can('case_item_priority.*.delete') || $user->can("case_item_priority.{$caseItemPriority->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this case item priority.');
    }

    public function restore(User $user, ServiceRequestPriority $caseItemPriority): Response
    {
        return $user->can('case_item_priority.*.restore') || $user->can("case_item_priority.{$caseItemPriority->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this case item priority.');
    }

    public function forceDelete(User $user, ServiceRequestPriority $caseItemPriority): Response
    {
        return $user->can('case_item_priority.*.force-delete') || $user->can("case_item_priority.{$caseItemPriority->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to force delete this case item priority.');
    }
}
