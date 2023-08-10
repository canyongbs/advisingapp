<?php

namespace Assist\KnowledgeBase\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;

class KnowledgeBaseItemPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('knowledge_base_item.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view knowledge base items.');
    }

    public function view(User $user, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $user->can('knowledge_base_item.*.view') || $user->can("knowledge_base_item.{$knowledgeBaseItem->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this knowledge base item.');
    }

    public function create(User $user): Response
    {
        return $user->can('knowledge_base_item.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create knowledge base items.');
    }

    public function update(User $user, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $user->can('knowledge_base_item.*.update') || $user->can("knowledge_base_item.{$knowledgeBaseItem->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this knowledge base item.');
    }

    public function delete(User $user, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $user->can('knowledge_base_item.*.delete') || $user->can("knowledge_base_item.{$knowledgeBaseItem->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this knowledge base item.');
    }

    public function restore(User $user, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $user->can('knowledge_base_item.*.restore') || $user->can("knowledge_base_item.{$knowledgeBaseItem->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this knowledge base item.');
    }

    public function forceDelete(User $user, KnowledgeBaseItem $knowledgeBaseItem): Response
    {
        return $user->can('knowledge_base_item.*.force-delete') || $user->can("knowledge_base_item.{$knowledgeBaseItem->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to force delete this knowledge base item.');
    }
}
