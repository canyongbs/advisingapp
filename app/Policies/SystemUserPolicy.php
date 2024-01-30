<?php

namespace App\Policies;

use App\Models\Authenticatable;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SystemUserPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['system_user.view-any'],
            denyResponse: 'You do not have permission to view users.'
        );
    }

    public function view(Authenticatable $authenticatable, SystemUser $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['system_user.*.view', "system_user.{$model->id}.view"],
            denyResponse: 'You do not have permission to view this user.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'system_user.create',
            denyResponse: 'You do not have permission to create users.'
        );
    }

    public function update(Authenticatable $authenticatable, SystemUser $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['system_user.*.update', "system_user.{$model->id}.update"],
            denyResponse: 'You do not have permission to update this user.'
        );
    }

    public function delete(Authenticatable $authenticatable, SystemUser $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['system_user.*.delete', "system_user.{$model->id}.delete"],
            denyResponse: 'You do not have permission to delete this user.'
        );
    }

    public function restore(Authenticatable $authenticatable, SystemUser $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['system_user.*.restore', "system_user.{$model->id}.restore"],
            denyResponse: 'You do not have permission to restore this user.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, SystemUser $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['system_user.*.force-delete', "system_user.{$model->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this user.'
        );
    }
}
