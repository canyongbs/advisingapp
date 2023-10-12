<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'user.view-any',
            denyResponse: 'You do not have permission to view users.'
        );
    }

    public function view(User $user, User $model): Response
    {
        return $user->canOrElse(
            abilities: ['user.*.view', "user.{$model->id}.view"],
            denyResponse: 'You do not have permission to view this user.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'user.create',
            denyResponse: 'You do not have permission to create users.'
        );
    }

    public function update(User $user, User $model): Response
    {
        return $user->canOrElse(
            abilities: ['user.*.update', "user.{$model->id}.update"],
            denyResponse: 'You do not have permission to update this user.'
        );
    }

    public function delete(User $user, User $model): Response
    {
        return $user->canOrElse(
            abilities: ['user.*.delete', "user.{$model->id}.delete"],
            denyResponse: 'You do not have permission to delete this user.'
        );
    }

    public function restore(User $user, User $model): Response
    {
        return $user->canOrElse(
            abilities: ['user.*.restore', "user.{$model->id}.restore"],
            denyResponse: 'You do not have permission to restore this user.'
        );
    }

    public function forceDelete(User $user, User $model): Response
    {
        return $user->canOrElse(
            abilities: ['user.*.force-delete', "user.{$model->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this user.'
        );
    }
}
