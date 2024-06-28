<?php

namespace AdvisingApp\Ai\Events;

use App\Models\User;
use AdvisingApp\Ai\Enums\AiModel;
use Illuminate\Support\Collection;
use AdvisingApp\Ai\Models\AiAssistant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class AssistantFilesFinishedUploading
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public User $user,
        public AiModel $model,
        public AiAssistant $assistant,
        public Collection $files,
    ) {}
}
