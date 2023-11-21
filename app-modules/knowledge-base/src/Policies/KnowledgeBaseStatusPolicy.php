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

namespace Assist\KnowledgeBase\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;

class KnowledgeBaseStatusPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'knowledge_base_status.view-any',
            denyResponse: 'You do not have permission to view any knowledge base statuses.'
        );
    }

    public function view(User $user, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_status.*.view', "knowledge_base_status.{$knowledgeBaseStatus->id}.view"],
            denyResponse: 'You do not have permission to view this knowledge base status.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'knowledge_base_status.create',
            denyResponse: 'You do not have permission to create knowledge base statuses.'
        );
    }

    public function update(User $user, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_status.*.update', "knowledge_base_status.{$knowledgeBaseStatus->id}.update"],
            denyResponse: 'You do not have permission to update this knowledge base status.'
        );
    }

    public function delete(User $user, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_status.*.delete', "knowledge_base_status.{$knowledgeBaseStatus->id}.delete"],
            denyResponse: 'You do not have permission to delete this knowledge base status.'
        );
    }

    public function restore(User $user, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_status.*.restore', "knowledge_base_status.{$knowledgeBaseStatus->id}.restore"],
            denyResponse: 'You do not have permission to restore this knowledge base status.'
        );
    }

    public function forceDelete(User $user, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_status.*.force-delete', "knowledge_base_status.{$knowledgeBaseStatus->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this knowledge base status.'
        );
    }
}
