<?php

namespace Assist\Audit\Policies;

use App\Models\User;
use Assist\Audit\Models\Audit;
use Illuminate\Auth\Access\Response;

class AuditPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('audit.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view audits.');
    }

    public function view(User $user, Audit $audit): Response
    {
        return $user->can('audit.*.view') || $user->can("audit.{$audit->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this audit.');
    }

    public function create(User $user): Response
    {
        return $user->can('audit.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create audits.');
    }

    public function update(User $user, Audit $audit): Response
    {
        return $user->can('audit.*.update') || $user->can("audit.{$audit->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this audit.');
    }

    public function delete(User $user, Audit $audit): Response
    {
        return $user->can('audit.*.delete') || $user->can("audit.{$audit->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this audit.');
    }

    public function restore(User $user, Audit $audit): Response
    {
        return $user->can('audit.*.restore') || $user->can("audit.{$audit->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this audit.');
    }

    public function forceDelete(User $user, Audit $audit): Response
    {
        return $user->can('audit.*.force-delete') || $user->can("audit.{$audit->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to force delete this audit.');
    }
}
