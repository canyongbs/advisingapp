<?php

namespace Assist\CaseloadManagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\CaseloadManagement\Models\Caseload;

class CaseloadPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'caseload.view-any',
            denyResponse: 'You do not have permission to view caseloads.'
        );
    }

    public function view(User $user, Caseload $caseload): Response
    {
        return $user->canOrElse(
            abilities: ['caseload.*.view', "caseload.{$caseload->id}.view"],
            denyResponse: 'You do not have permission to view this caseload.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'caseload.create',
            denyResponse: 'You do not have permission to create caseloads.'
        );
    }

    public function update(User $user, Caseload $caseload): Response
    {
        return $user->canOrElse(
            abilities: ['caseload.*.update', "caseload.{$caseload->id}.update"],
            denyResponse: 'You do not have permission to update this caseload.'
        );
    }

    public function delete(User $user, Caseload $caseload): Response
    {
        return $user->canOrElse(
            abilities: ['caseload.*.delete', "caseload.{$caseload->id}.delete"],
            denyResponse: 'You do not have permission to delete this caseload.'
        );
    }

    public function restore(User $user, Caseload $caseload): Response
    {
        return $user->canOrElse(
            abilities: ['caseload.*.restore', "caseload.{$caseload->id}.restore"],
            denyResponse: 'You do not have permission to restore this caseload.'
        );
    }

    public function forceDelete(User $user, Caseload $caseload): Response
    {
        return $user->canOrElse(
            abilities: ['caseload.*.force-delete', "caseload.{$caseload->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this caseload.'
        );
    }
}
