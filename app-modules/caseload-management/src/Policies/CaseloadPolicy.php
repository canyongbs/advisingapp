<?php

namespace Assist\CaseloadManagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\CaseloadManagement\Models\Caseload;

class CaseloadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            'caseload.view-any',
            'You do not have permission to view caseloads.'
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Caseload $caseload): Response
    {
        return $user->canOrElse(
            ['caseload.*.view', "caseload.{$caseload->id}.view"],
            'You do not have permission to view this caseload.'
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->canOrElse(
            'caseload.create',
            'You do not have permission to create caseloads.'
        );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Caseload $caseload): Response
    {
        return $user->canOrElse(
            ['caseload.*.update', "caseload.{$caseload->id}.update"],
            'You do not have permission to update this caseload.'
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Caseload $caseload): Response
    {
        return $user->canOrElse(
            ['caseload.*.delete', "caseload.{$caseload->id}.delete"],
            'You do not have permission to delete this caseload.'
        );
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Caseload $caseload): Response
    {
        return $user->canOrElse(
            ['caseload.*.restore', "caseload.{$caseload->id}.restore"],
            'You do not have permission to restore this caseload.'
        );
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Caseload $caseload): Response
    {
        return $user->canOrElse(
            ['caseload.*.force-delete', "caseload.{$caseload->id}.force-delete"],
            'You do not have permission to permanently delete this caseload.'
        );
    }
}
