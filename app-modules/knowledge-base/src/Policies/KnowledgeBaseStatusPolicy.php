<?php

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
