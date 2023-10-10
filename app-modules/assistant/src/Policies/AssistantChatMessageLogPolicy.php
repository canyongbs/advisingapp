<?php

namespace Assist\Assistant\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Assistant\Models\AssistantChatMessageLog;

class AssistantChatMessageLogPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'assistant_chat_message_log.view-any',
            denyResponse: 'You do not have permission to view assistant chat message logs.'
        );
    }

    public function view(User $user, AssistantChatMessageLog $assistantChatMessageLog): Response
    {
        return $user->canOrElse(
            abilities: ['assistant_chat_message_log.*.view', "assistant_chat_message_log.{$assistantChatMessageLog->id}.view"],
            denyResponse: 'You do not have permission to view this assistant chat message log.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'assistant_chat_message_log.create',
            denyResponse: 'You do not have permission to create assistant chat message logs.'
        );
    }

    public function update(User $user, AssistantChatMessageLog $assistantChatMessageLog): Response
    {
        return $user->canOrElse(
            abilities: ['assistant_chat_message_log.*.update', "assistant_chat_message_log.{$assistantChatMessageLog->id}.update"],
            denyResponse: 'You do not have permission to update this assistant chat message log.'
        );
    }

    public function delete(User $user, AssistantChatMessageLog $assistantChatMessageLog): Response
    {
        return $user->canOrElse(
            abilities: ['assistant_chat_message_log.*.delete', "assistant_chat_message_log.{$assistantChatMessageLog->id}.delete"],
            denyResponse: 'You do not have permission to delete this assistant chat message log.'
        );
    }

    public function restore(User $user, AssistantChatMessageLog $assistantChatMessageLog): Response
    {
        return $user->canOrElse(
            abilities: ['assistant_chat_message_log.*.restore', "assistant_chat_message_log.{$assistantChatMessageLog->id}.restore"],
            denyResponse: 'You do not have permission to restore this assistant chat message log.'
        );
    }

    public function forceDelete(User $user, AssistantChatMessageLog $assistantChatMessageLog): Response
    {
        return $user->canOrElse(
            abilities: ['assistant_chat_message_log.*.force-delete', "assistant_chat_message_log.{$assistantChatMessageLog->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this assistant chat message log.'
        );
    }
}
