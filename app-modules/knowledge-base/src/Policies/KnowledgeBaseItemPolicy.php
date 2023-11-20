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
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;

class KnowledgeBaseItemPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'knowledge_base_item.view-any',
            denyResponse: 'You do not have permissions to view knowledge base items.'
        );
    }

    public function view(User $user, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_item.*.view', "knowledge_base_item.{$knowledgeBaseItem->id}.view"],
            denyResponse: 'You do not have permissions to view this knowledge base item.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'knowledge_base_item.create',
            denyResponse: 'You do not have permissions to create knowledge base items.'
        );
    }

    public function update(User $user, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_item.*.update', "knowledge_base_item.{$knowledgeBaseItem->id}.update"],
            denyResponse: 'You do not have permissions to update this knowledge base item.'
        );
    }

    public function delete(User $user, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_item.*.delete', "knowledge_base_item.{$knowledgeBaseItem->id}.delete"],
            denyResponse: 'You do not have permissions to delete this knowledge base item.'
        );
    }

    public function restore(User $user, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_item.*.restore', "knowledge_base_item.{$knowledgeBaseItem->id}.restore"],
            denyResponse: 'You do not have permissions to restore this knowledge base item.'
        );
    }

    public function forceDelete(User $user, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_item.*.force-delete', "knowledge_base_item.{$knowledgeBaseItem->id}.force-delete"],
            denyResponse: 'You do not have permissions to force delete this knowledge base item.'
        );
    }
}
