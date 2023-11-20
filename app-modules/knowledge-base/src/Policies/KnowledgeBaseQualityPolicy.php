<?php

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
