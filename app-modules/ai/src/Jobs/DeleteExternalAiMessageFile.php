<?php

namespace AdvisingApp\Ai\Jobs;

use Illuminate\Bus\Queueable;
use AdvisingApp\Ai\Models\AiMessageFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\IntegrationOpenAi\Services\Concerns\UploadsFiles;

class DeleteExternalAiMessageFile implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public AiMessageFile $aiMessageFile;

    public function __construct(AiMessageFile $aiMessageFile)
    {
        $this->aiMessageFile = clone $aiMessageFile;

        $this->aiMessageFile->load([
            'message' => fn (BelongsTo $query) => $query->withTrashed(),
            'message.thread' => fn (BelongsTo $query) => $query->withTrashed(),
            'message.thread.assistant' => fn (BelongsTo $query) => $query->withTrashed(),
        ]);
    }

    public function handle(): void
    {
        if (empty($this->aiMessageFile->file_id)) {
            return;
        }

        $service = $this->aiMessageFile->message->thread->assistant->model->getService();

        if ($service->supportsMessageFileUploads() && in_array(UploadsFiles::class, class_uses_recursive($service::class))) {
            $service->deleteFile($this->aiMessageFile);
        }
    }
}
