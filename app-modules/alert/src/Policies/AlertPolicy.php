<?php

namespace Assist\Alert\Policies;

use App\Models\User;
use Assist\Alert\Models\Alert;
use Illuminate\Auth\Access\Response;

class AlertPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'alert.view-any',
            denyResponse: 'You do not have permission to view alerts.'
        );
    }

    public function view(User $user, Alert $alert): Response
    {
        return $user->canOrElse(
            abilities: ['alert.*.view', "alert.{$alert->id}.view"],
            denyResponse: 'You do not have permission to view this alert.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'alert.create',
            denyResponse: 'You do not have permission to create alerts.'
        );
    }

    public function update(User $user, Alert $alert): Response
    {
        return $user->canOrElse(
            abilities: ['alert.*.update', "alert.{$alert->id}.update"],
            denyResponse: 'You do not have permission to update this alert.'
        );
    }

    public function delete(User $user, Alert $alert): Response
    {
        return $user->canOrElse(
            abilities: ['alert.*.delete', "alert.{$alert->id}.delete"],
            denyResponse: 'You do not have permission to delete this alert.'
        );
    }

    public function restore(User $user, Alert $alert): Response
    {
        return $user->canOrElse(
            abilities: ['alert.*.restore', "alert.{$alert->id}.restore"],
            denyResponse: 'You do not have permission to restore this alert.'
        );
    }

    public function forceDelete(User $user, Alert $alert): Response
    {
        return $user->canOrElse(
            abilities: ['alert.*.force-delete', "alert.{$alert->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this alert.'
        );
    }
}
