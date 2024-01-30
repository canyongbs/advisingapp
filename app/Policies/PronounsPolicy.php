<?php

namespace App\Policies;

use App\Models\Authenticatable;
use App\Models\Pronouns;
use Illuminate\Auth\Access\Response;

class PronounsPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['pronouns.view-any'],
            denyResponse: 'You do not have permission to view pronouns.'
        );
    }

    public function view(Authenticatable $authenticatable, Pronouns $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['pronouns.*.view', "pronouns.{$model->id}.view"],
            denyResponse: 'You do not have permission to view these pronouns.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'pronouns.create',
            denyResponse: 'You do not have permission to create pronouns.'
        );
    }

    public function update(Authenticatable $authenticatable, Pronouns $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['pronouns.*.update', "pronouns.{$model->id}.update"],
            denyResponse: 'You do not have permission to update these pronouns.'
        );
    }

    public function delete(Authenticatable $authenticatable, Pronouns $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['pronouns.*.delete', "pronouns.{$model->id}.delete"],
            denyResponse: 'You do not have permission to delete these pronouns.'
        );
    }

    public function restore(Authenticatable $authenticatable, Pronouns $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['pronouns.*.restore', "pronouns.{$model->id}.restore"],
            denyResponse: 'You do not have permission to restore these pronouns.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Pronouns $model): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['pronouns.*.force-delete', "pronouns.{$model->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete these pronouns.'
        );
    }
}
