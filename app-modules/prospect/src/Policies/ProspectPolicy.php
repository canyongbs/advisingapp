<?php

namespace Assist\Prospect\Policies;

use App\Models\User;
use Assist\Prospect\Models\Prospect;
use Illuminate\Auth\Access\Response;

class ProspectPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect.view-any',
            denyResponse: 'You do not have permission to view prospects.'
        );
    }

    public function view(User $user, Prospect $prospect): Response
    {
        return $user->canOrElse(
            abilities: ['prospect.*.view', "prospect.{$prospect->id}.view"],
            denyResponse: 'You do not have permission to view this prospect.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect.create',
            denyResponse: 'You do not have permission to create prospects.'
        );
    }

    public function import(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect.import',
            denyResponse: 'You do not have permission to import prospects.',
        );
    }

    public function update(User $user, Prospect $prospect): Response
    {
        return $user->canOrElse(
            abilities: ['prospect.*.update', "prospect.{$prospect->id}.update"],
            denyResponse: 'You do not have permission to update this prospect.'
        );
    }

    public function delete(User $user, Prospect $prospect): Response
    {
        return $user->canOrElse(
            abilities: ['prospect.*.delete', "prospect.{$prospect->id}.delete"],
            denyResponse: 'You do not have permission to delete this prospect.'
        );
    }

    public function restore(User $user, Prospect $prospect): Response
    {
        return $user->canOrElse(
            abilities: ['prospect.*.restore', "prospect.{$prospect->id}.restore"],
            denyResponse: 'You do not have permission to restore this prospect.'
        );
    }

    public function forceDelete(User $user, Prospect $prospect): Response
    {
        return $user->canOrElse(
            abilities: ['prospect.*.force-delete', "prospect.{$prospect->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this prospect.'
        );
    }
}
