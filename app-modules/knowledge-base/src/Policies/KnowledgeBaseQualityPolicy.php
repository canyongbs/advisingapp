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
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;

class KnowledgeBaseQualityPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'knowledge_base_quality.view-any',
            denyResponse: 'You do not have permission to view any knowledge base categories.'
        );
    }

    public function view(User $user, KnowledgeBaseQuality $knowledgeBaseQuality): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_quality.*.view', "knowledge_base_quality.{$knowledgeBaseQuality->id}.view"],
            denyResponse: 'You do not have permission to view this knowledge base category.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'knowledge_base_quality.create',
            denyResponse: 'You do not have permission to create knowledge base categories.'
        );
    }

    public function update(User $user, KnowledgeBaseQuality $knowledgeBaseQuality): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_quality.*.update', "knowledge_base_quality.{$knowledgeBaseQuality->id}.update"],
            denyResponse: 'You do not have permission to update this knowledge base category.'
        );
    }

    public function delete(User $user, KnowledgeBaseQuality $knowledgeBaseQuality): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_quality.*.delete', "knowledge_base_quality.{$knowledgeBaseQuality->id}.delete"],
            denyResponse: 'You do not have permission to delete this knowledge base category.'
        );
    }

    public function restore(User $user, KnowledgeBaseQuality $knowledgeBaseQuality): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_quality.*.restore', "knowledge_base_quality.{$knowledgeBaseQuality->id}.restore"],
            denyResponse: 'You do not have permission to restore this knowledge base category.'
        );
    }

    public function forceDelete(User $user, KnowledgeBaseQuality $knowledgeBaseQuality): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_quality.*.force-delete', "knowledge_base_quality.{$knowledgeBaseQuality->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this knowledge base category.'
        );
    }
}
