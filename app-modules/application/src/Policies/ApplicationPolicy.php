<?php

namespace Assist\Application\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Application\Models\Application;

class ApplicationPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'application.view-any',
            denyResponse: 'You do not have permission to view applications.'
        );
    }

    public function view(User $user, Application $application): Response
    {
        return $user->canOrElse(
            abilities: ['application.*.view', "application.{$application->id}.view"],
            denyResponse: 'You do not have permission to view this application.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'application.create',
            denyResponse: 'You do not have permission to create applications.'
        );
    }

    public function update(User $user, Application $application): Response
    {
        return $user->canOrElse(
            abilities: ['application.*.update', "application.{$application->id}.update"],
            denyResponse: 'You do not have permission to update this application.'
        );
    }

    public function delete(User $user, Application $application): Response
    {
        return $user->canOrElse(
            abilities: ['application.*.delete', "application.{$application->id}.delete"],
            denyResponse: 'You do not have permission to delete this application.'
        );
    }

    public function restore(User $user, Application $application): Response
    {
        return $user->canOrElse(
            abilities: ['application.*.restore', "application.{$application->id}.restore"],
            denyResponse: 'You do not have permission to restore this application.'
        );
    }

    public function forceDelete(User $user, Application $application): Response
    {
        return $user->canOrElse(
            abilities: ['application.*.force-delete', "application.{$application->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this application.'
        );
    }
}
