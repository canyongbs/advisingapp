<?php

namespace Assist\KnowledgeBase\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;

class KnowledgeBaseCategoryPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('knowledge_base_category.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view knowledge base categories.');
    }

    public function view(User $user, KnowledgeBaseCategory $knowledgeBaseCategory): Response
    {
        return $user->can('knowledge_base_category.*.view') || $user->can("knowledge_base_category.{$knowledgeBaseCategory->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this knowledge base category.');
    }

    public function create(User $user): Response
    {
        return $user->can('knowledge_base_category.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create knowledge base categories.');
    }

    public function update(User $user, KnowledgeBaseCategory $knowledgeBaseCategory): Response
    {
        return $user->can('knowledge_base_category.*.update') || $user->can("knowledge_base_category.{$knowledgeBaseCategory->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this knowledge base category.');
    }

    public function delete(User $user, KnowledgeBaseCategory $knowledgeBaseCategory): Response
    {
        return $user->can('knowledge_base_category.*.delete') || $user->can("knowledge_base_category.{$knowledgeBaseCategory->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this knowledge base category.');
    }

    public function restore(User $user, KnowledgeBaseCategory $knowledgeBaseCategory): Response
    {
        return $user->can('knowledge_base_category.*.restore') || $user->can("knowledge_base_category.{$knowledgeBaseCategory->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this knowledge base category.');
    }

    public function forceDelete(User $user, KnowledgeBaseCategory $knowledgeBaseCategory): Response
    {
        return $user->can('knowledge_base_category.*.force-delete') || $user->can("knowledge_base_category.{$knowledgeBaseCategory->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to permanently delete this knowledge base category.');
    }
}
