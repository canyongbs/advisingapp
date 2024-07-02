<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Ai\Filament\Resources\AiAssistantResource\Concerns;

use Throwable;
use Illuminate\Support\Collection;
use AdvisingApp\Ai\Models\AiAssistant;
use Filament\Notifications\Notification;
use AdvisingApp\Ai\Models\AiAssistantFile;
use AdvisingApp\Ai\Services\Contracts\AiService;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt4Service;
use AdvisingApp\IntegrationOpenAi\Jobs\UploadFilesToAssistant;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt35Service;
use AdvisingApp\IntegrationOpenAi\Services\OpenAiGpt4oService;

trait HandlesFileUploads
{
    protected function attemptingToUploadAssistantFilesWhenItsNotSupported(AiService $service, array $data): bool
    {
        if (isset($data['uploaded_files']) && $service->supportsAssistantFileUploads() === false) {
            Notification::make()
                ->title('Files could not be uploaded to your custom assistant.')
                ->body('It looks like you attempted to upload files to your custom assistant, but it is not currently supported on the selected model.')
                ->danger()
                ->send();

            return true;
        }

        return false;
    }

    protected function uploadFilesToAssistant(AiService $aiService, AiAssistant $assistant, array $uploadedFiles): void
    {
        $aiAssistantFiles = $this->createAiAssistantFiles($assistant, $uploadedFiles);

        try {
            match (true) {
                $aiService instanceof OpenAiGpt4oService => UploadFilesToAssistant::dispatchSync($aiService, $assistant, $aiAssistantFiles),
                $aiService instanceof OpenAiGpt4Service => UploadFilesToAssistant::dispatchSync($aiService, $assistant, $aiAssistantFiles),
                $aiService instanceof OpenAiGpt35Service => UploadFilesToAssistant::dispatchSync($aiService, $assistant, $aiAssistantFiles),
                default => $this->couldNotUploadFilesToAssistant($aiAssistantFiles),
            };
        } catch (Throwable $e) {
            $this->failedToUploadFilesToAssistant($aiAssistantFiles);

            report($e);
        }
    }

    protected function createAiAssistantFiles(AiAssistant $record, array $uploadedFiles): Collection
    {
        return collect($uploadedFiles)
            ->map(function ($file) use ($record): AiAssistantFile {
                $fileRecord = new AiAssistantFile();
                $fileRecord->temporary_url = $file->temporaryUrl();
                $fileRecord->name = $file->getClientOriginalName();
                $fileRecord->mime_type = $file->getMimeType();
                $fileRecord->assistant()->associate($record);
                $fileRecord->save();

                return $fileRecord;
            });
    }

    protected function couldNotUploadFilesToAssistant(Collection $aiAssistantFiles): void
    {
        Notification::make()
            ->title('Files could not be uploaded to your custom assistant.')
            ->body('It looks like you attempted to upload files to your custom assistant, but it is not currently supported on the selected model.')
            ->danger()
            ->send();

        $aiAssistantFiles->each->forceDelete();
    }

    // TODO Perhaps implement some sort of background retry mechanism
    protected function failedToUploadFilesToAssistant(Collection $files): void
    {
        Notification::make()
            ->title('Files failed to upload to custom assistant')
            ->body('The files you tried to attach failed to upload to the custom assistant. Support has been notified about this problem. Please try again later.')
            ->danger()
            ->send();

        $files->each(function (AiAssistantFile $file) {
            if (blank($file->file_id)) {
                $file->delete();
            }
        });
    }
}
