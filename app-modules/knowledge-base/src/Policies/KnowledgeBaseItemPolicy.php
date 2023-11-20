<?php

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
