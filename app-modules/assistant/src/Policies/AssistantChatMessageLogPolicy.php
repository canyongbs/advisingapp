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
        return Response::deny('Assistant chat message logs cannot be created.');
    }

    public function update(User $user, AssistantChatMessageLog $assistantChatMessageLog): Response
    {
        return Response::deny('Assistant chat message logs cannot be updated.');
    }

    public function delete(User $user, AssistantChatMessageLog $assistantChatMessageLog): Response
    {
        return Response::deny('Assistant chat message logs cannot be deleted.');
    }

    public function restore(User $user, AssistantChatMessageLog $assistantChatMessageLog): Response
    {
        return Response::deny('Assistant chat message logs cannot be restored.');
    }

    public function forceDelete(User $user, AssistantChatMessageLog $assistantChatMessageLog): Response
    {
        return Response::deny('Assistant chat message logs cannot be force deleted.');
    }
}
