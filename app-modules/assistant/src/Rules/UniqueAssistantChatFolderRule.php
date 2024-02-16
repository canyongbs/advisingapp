<?php

namespace AdvisingApp\Assistant\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use AdvisingApp\Assistant\Models\AssistantChatFolder;

class UniqueAssistantChatFolderRule implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user_id = $this->data['input']['user_id'] ?? AssistantChatFolder::find($this->data['id'])->user_id;

        if (
            AssistantChatFolder::query()
                ->where('name', $value)
                ->where('user_id', $user_id)
                ->exists()
        ) {
            $fail("This user already has a folder named {$value}.");
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
