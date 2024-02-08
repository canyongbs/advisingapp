<?php

namespace AdvisingApp\InAppCommunication\Actions;

use App\Models\User;

class CheckConversationMessageContentForMention
{
    public function __invoke(array $content, User $participant, bool $isRoot = true): bool
    {
        foreach (($isRoot ? [$content] : $content) as $component) {
            if (
                ($component['type'] === 'mention') &&
                ($component['attrs']['id'] === $participant->getKey())
            ) {
                return true;
            }

            if ($this($component['content'] ?? [], $participant, isRoot: false)) {
                return true;
            }
        }

        return false;
    }
}
