<?php

namespace Assist\Audit\Policies;

use App\Models\User;
use Assist\Audit\Models\Audit;
use Illuminate\Auth\Access\Response;

class AuditPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'audit.view-any',
            denyResponse: 'You do not have permission to view audits.'
        );
    }

    public function view(User $user, Audit $audit): Response
    {
        return $user->canOrElse(
            abilities: ['audit.*.view', "audit.{$audit->id}.view"],
            denyResponse: 'You do not have permission to view this audit.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'audit.create',
            denyResponse: 'You do not have permission to create audits.'
        );
    }

    public function update(User $user, Audit $audit): Response
    {
        return $user->canOrElse(
            abilities: ['audit.*.update', "audit.{$audit->id}.update"],
            denyResponse: 'You do not have permission to update this audit.'
        );
    }

    public function delete(User $user, Audit $audit): Response
    {
        return $user->canOrElse(
            abilities: ['audit.*.delete', "audit.{$audit->id}.delete"],
            denyResponse: 'You do not have permission to delete this audit.'
        );
    }

    public function restore(User $user, Audit $audit): Response
    {
        return $user->canOrElse(
            abilities: ['audit.*.restore', "audit.{$audit->id}.restore"],
            denyResponse: 'You do not have permission to restore this audit.'
        );
    }

    public function forceDelete(User $user, Audit $audit): Response
    {
        return $user->canOrElse(
            abilities: ['audit.*.force-delete', "audit.{$audit->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this audit.'
        );
    }
}
