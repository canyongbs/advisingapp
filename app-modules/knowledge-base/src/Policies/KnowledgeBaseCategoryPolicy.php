<?php

namespace Assist\KnowledgeBase\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;

class KnowledgeBaseCategoryPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'knowledge_base_category.view-any',
            denyResponse: 'You do not have permissions to view knowledge base categories.'
        );
    }

    public function view(User $user, KnowledgeBaseCategory $knowledgeBaseCategory): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_category.*.view', "knowledge_base_category.{$knowledgeBaseCategory->id}.view"],
            denyResponse: 'You do not have permissions to view this knowledge base category.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'knowledge_base_category.create',
            denyResponse: 'You do not have permissions to create knowledge base categories.'
        );
    }

    public function update(User $user, KnowledgeBaseCategory $knowledgeBaseCategory): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_category.*.update', "knowledge_base_category.{$knowledgeBaseCategory->id}.update"],
            denyResponse: 'You do not have permissions to update this knowledge base category.'
        );
    }

    public function delete(User $user, KnowledgeBaseCategory $knowledgeBaseCategory): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_category.*.delete', "knowledge_base_category.{$knowledgeBaseCategory->id}.delete"],
            denyResponse: 'You do not have permissions to delete this knowledge base category.'
        );
    }

    public function restore(User $user, KnowledgeBaseCategory $knowledgeBaseCategory): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_category.*.restore', "knowledge_base_category.{$knowledgeBaseCategory->id}.restore"],
            denyResponse: 'You do not have permissions to restore this knowledge base category.'
        );
    }

    public function forceDelete(User $user, KnowledgeBaseCategory $knowledgeBaseCategory): Response
    {
        return $user->canOrElse(
            abilities: ['knowledge_base_category.*.force-delete', "knowledge_base_category.{$knowledgeBaseCategory->id}.force-delete"],
            denyResponse: 'You do not have permissions to permanently delete this knowledge base category.'
        );
    }
}
