<?php

namespace Assist\Case\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Case\Models\ServiceRequestStatus;

class CaseItemStatusPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('case_item_status.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view case item statuses.');
    }

    public function view(User $user, ServiceRequestStatus $caseItemStatus): Response
    {
        return $user->can('case_item_status.*.view') || $user->can("case_item_status.{$caseItemStatus->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this case item status.');
    }

    public function create(User $user): Response
    {
        return $user->can('case_item_status.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create case item statuses.');
    }

    public function update(User $user, ServiceRequestStatus $caseItemStatus): Response
    {
        return $user->can('case_item_status.*.update') || $user->can("case_item_status.{$caseItemStatus->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this case item status.');
    }

    public function delete(User $user, ServiceRequestStatus $caseItemStatus): Response
    {
        return $user->can('case_item_status.*.delete') || $user->can("case_item_status.{$caseItemStatus->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this case item status.');
    }

    public function restore(User $user, ServiceRequestStatus $caseItemStatus): Response
    {
        return $user->can('case_item_status.*.restore') || $user->can("case_item_status.{$caseItemStatus->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this case item status.');
    }

    public function forceDelete(User $user, ServiceRequestStatus $caseItemStatus): Response
    {
        return $user->can('case_item_status.*.force-delete') || $user->can("case_item_status.{$caseItemStatus->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to force delete this case item status.');
    }
}
