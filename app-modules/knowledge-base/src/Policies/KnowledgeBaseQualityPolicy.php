<?php

namespace Assist\KnowledgeBase\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;

class KnowledgeBaseQualityPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('knowledge_base_quality.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view any knowledge base categories.');
    }

    public function view(User $user, KnowledgeBaseQuality $knowledgeBaseQuality): Response
    {
        return $user->can('knowledge_base_quality.*.view') || $user->can("knowledge_base_quality.{$knowledgeBaseQuality->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this knowledge base category.');
    }

    public function create(User $user): Response
    {
        return $user->can('knowledge_base_quality.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create knowledge base categories.');
    }

    public function update(User $user, KnowledgeBaseQuality $knowledgeBaseQuality): Response
    {
        return $user->can('knowledge_base_quality.*.update') || $user->can("knowledge_base_quality.{$knowledgeBaseQuality->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this knowledge base category.');
    }

    public function delete(User $user, KnowledgeBaseQuality $knowledgeBaseQuality): Response
    {
        return $user->can('knowledge_base_quality.*.delete') || $user->can("knowledge_base_quality.{$knowledgeBaseQuality->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this knowledge base category.');
    }

    public function restore(User $user, KnowledgeBaseQuality $knowledgeBaseQuality): Response
    {
        return $user->can('knowledge_base_quality.*.restore') || $user->can("knowledge_base_quality.{$knowledgeBaseQuality->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this knowledge base category.');
    }

    public function forceDelete(User $user, KnowledgeBaseQuality $knowledgeBaseQuality): Response
    {
        return $user->can('knowledge_base_quality.*.force-delete') || $user->can("knowledge_base_quality.{$knowledgeBaseQuality->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this knowledge base category.');
    }
}
