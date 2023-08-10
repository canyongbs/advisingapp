<?php

namespace Assist\KnowledgeBase\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;

class KnowledgeBaseStatusPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('knowledge_base_status.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view any knowledge base statuses.');
    }

    public function view(User $user, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        return $user->can('knowledge_base_status.*.view') || $user->can("knowledge_base_status.{$knowledgeBaseStatus->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this knowledge base status.');
    }

    public function create(User $user): Response
    {
        return $user->can('knowledge_base_status.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create knowledge base statuses.');
    }

    public function update(User $user, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        return $user->can('knowledge_base_status.*.update') || $user->can("knowledge_base_status.{$knowledgeBaseStatus->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this knowledge base status.');
    }

    public function delete(User $user, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        return $user->can('knowledge_base_status.*.delete') || $user->can("knowledge_base_status.{$knowledgeBaseStatus->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this knowledge base status.');
    }

    public function restore(User $user, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        return $user->can('knowledge_base_status.*.restore') || $user->can("knowledge_base_status.{$knowledgeBaseStatus->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this knowledge base status.');
    }

    public function forceDelete(User $user, KnowledgeBaseStatus $knowledgeBaseStatus): Response
    {
        return $user->can('knowledge_base_status.*.force-delete') || $user->can("knowledge_base_status.{$knowledgeBaseStatus->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this knowledge base status.');
    }
}
