<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Campaign\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Campaign\Models\CampaignAction;

class CampaignActionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'campaign_action.view-any',
            denyResponse: 'You do not have permission to view campaign actions.'
        );
    }

    public function view(User $user, CampaignAction $campaignAction): Response
    {
        return $user->canOrElse(
            abilities: ['campaign_action.*.view', "campaign_action.{$campaignAction->id}.view"],
            denyResponse: 'You do not have permission to view this campaign action.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'campaign_action.create',
            denyResponse: 'You do not have permission to create campaign actions.'
        );
    }

    public function update(User $user, CampaignAction $campaignAction): Response
    {
        return $user->canOrElse(
            abilities: ['campaign_action.*.update', "campaign_action.{$campaignAction->id}.update"],
            denyResponse: 'You do not have permission to update this campaign action.'
        );
    }

    public function delete(User $user, CampaignAction $campaignAction): Response
    {
        return $user->canOrElse(
            abilities: ['campaign_action.*.delete', "campaign_action.{$campaignAction->id}.delete"],
            denyResponse: 'You do not have permission to delete this campaign action.'
        );
    }

    public function restore(User $user, CampaignAction $campaignAction): Response
    {
        return $user->canOrElse(
            abilities: ['campaign_action.*.restore', "campaign_action.{$campaignAction->id}.restore"],
            denyResponse: 'You do not have permission to restore this campaign action.'
        );
    }

    public function forceDelete(User $user, CampaignAction $campaignAction): Response
    {
        return $user->canOrElse(
            abilities: ['campaign_action.*.force-delete', "campaign_action.{$campaignAction->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this campaign action.'
        );
    }
}
