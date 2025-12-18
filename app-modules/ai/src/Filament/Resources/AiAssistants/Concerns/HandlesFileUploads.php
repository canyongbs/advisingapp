<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Ai\Filament\Resources\AiAssistants\Concerns;

use AdvisingApp\Ai\Actions\UploadFileForParsing;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Ai\Models\AiAssistantFile;
use Filament\Notifications\Notification;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait HandlesFileUploads
{
    /**
     * @param array<?TemporaryUploadedFile> $files
     */
    protected function uploadFilesToAssistant(AiAssistant $assistant, array $files): void
    {
        foreach ($files as $attachment) {
            if (! ($attachment instanceof TemporaryUploadedFile)) {
                continue;
            }

            $file = new AiAssistantFile();
            $file->assistant()->associate($assistant);
            $file->name = $attachment->getClientOriginalName();
            $file->mime_type = $attachment->getMimeType();
            $file->temporary_url = $attachment->temporaryUrl();

            $result = app(UploadFileForParsing::class)->execute(
                path: $attachment->getRealPath(),
                name: $file->name,
                mimeType: $file->mime_type,
            );

            if (blank($result->fileId) && blank($result->parsingResults)) {
                Notification::make()
                    ->title('File Upload Failed')
                    ->body('There was an error uploading the file. Please try again later.')
                    ->danger()
                    ->send();

                continue;
            }

            $file->file_id = $result->fileId;
            $file->parsing_results = $result->parsingResults;
            $file->save();

            $file->addMediaFromUrl($file->temporary_url)->toMediaCollection('files');
        }
    }
}
