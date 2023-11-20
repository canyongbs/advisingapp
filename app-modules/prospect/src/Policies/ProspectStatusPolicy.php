<?php

namespace Assist\Prospect\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Prospect\Models\ProspectStatus;

class ProspectStatusPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect_status.view-any',
            denyResponse: 'You do not have permission to view prospect statuses.'
        );
    }

    public function view(User $user, ProspectStatus $prospectStatus): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_status.*.view', "prospect_status.{$prospectStatus->id}.view"],
            denyResponse: 'You do not have permission to view prospect statuses.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect_status.create',
            denyResponse: 'You do not have permission to create prospect statuses.'
        );
    }

    public function update(User $user, ProspectStatus $prospectStatus): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_status.*.update', "prospect_status.{$prospectStatus->id}.update"],
            denyResponse: 'You do not have permission to update prospect statuses.'
        );
    }

    public function delete(User $user, ProspectStatus $prospectStatus): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_status.*.delete', "prospect_status.{$prospectStatus->id}.delete"],
            denyResponse: 'You do not have permission to delete prospect statuses.'
        );
    }

    public function restore(User $user, ProspectStatus $prospectStatus): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_status.*.restore', "prospect_status.{$prospectStatus->id}.restore"],
            denyResponse: 'You do not have permission to restore prospect statuses.'
        );
    }

    public function forceDelete(User $user, ProspectStatus $prospectStatus): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_status.*.force-delete', "prospect_status.{$prospectStatus->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete prospect statuses.'
        );
    }
}
