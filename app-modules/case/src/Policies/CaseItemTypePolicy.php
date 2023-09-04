<?php

namespace Assist\Case\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Case\Models\ServiceRequestType;

class CaseItemTypePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('case_item_type.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view case item types.');
    }

    public function view(User $user, ServiceRequestType $caseItemType): Response
    {
        return $user->can('case_item_type.*.view') || $user->can("case_item_type.{$caseItemType->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this case item type.');
    }

    public function create(User $user): Response
    {
        return $user->can('case_item_type.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create case item types.');
    }

    public function update(User $user, ServiceRequestType $caseItemType): Response
    {
        return $user->can('case_item_type.*.update') || $user->can("case_item_type.{$caseItemType->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this case item type.');
    }

    public function delete(User $user, ServiceRequestType $caseItemType): Response
    {
        return $user->can('case_item_type.*.delete') || $user->can("case_item_type.{$caseItemType->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this case item type.');
    }

    public function restore(User $user, ServiceRequestType $caseItemType): Response
    {
        return $user->can('case_item_type.*.restore') || $user->can("case_item_type.{$caseItemType->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this case item type.');
    }

    public function forceDelete(User $user, ServiceRequestType $caseItemType): Response
    {
        return $user->can('case_item_type.*.force-delete') || $user->can("case_item_type.{$caseItemType->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to force delete this case item type.');
    }
}
