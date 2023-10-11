<?php

namespace Assist\Prospect\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Prospect\Models\ProspectSource;

class ProspectSourcePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect_source.view-any',
            denyResponse: 'You do not have permission to view prospect sources.'
        );
    }

    public function view(User $user, ProspectSource $prospectSource): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_source.*.view', "prospect_source.{$prospectSource->id}.view"],
            denyResponse: 'You do not have permission to view this prospect source.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'prospect_source.create',
            denyResponse: 'You do not have permission to create prospect sources.'
        );
    }

    public function update(User $user, ProspectSource $prospectSource): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_source.*.update', "prospect_source.{$prospectSource->id}.update"],
            denyResponse: 'You do not have permission to update this prospect source.'
        );
    }

    public function delete(User $user, ProspectSource $prospectSource): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_source.*.delete', "prospect_source.{$prospectSource->id}.delete"],
            denyResponse: 'You do not have permission to delete this prospect source.'
        );
    }

    public function restore(User $user, ProspectSource $prospectSource): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_source.*.restore', "prospect_source.{$prospectSource->id}.restore"],
            denyResponse: 'You do not have permission to restore this prospect source.'
        );
    }

    public function forceDelete(User $user, ProspectSource $prospectSource): Response
    {
        return $user->canOrElse(
            abilities: ['prospect_source.*.force-delete', "prospect_source.{$prospectSource->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this prospect source.'
        );
    }
}
