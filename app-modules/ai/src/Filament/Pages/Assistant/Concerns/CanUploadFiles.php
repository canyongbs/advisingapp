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

namespace AdvisingApp\Ai\Filament\Pages\Assistant\Concerns;

use Filament\Actions\Action;
use Livewire\Attributes\Locked;
use Filament\Forms\Components\FileUpload;

trait CanUploadFiles
{
    #[Locked]
    public array $files = [];

    public function removeUploadedFile(int $key): void
    {
        unset($this->files[$key]);
    }

    public function clearFiles(): void
    {
        $this->reset('files');
    }

    public function uploadFilesAction(): Action
    {
        return Action::make('uploadFiles')
            ->label('Upload Files')
            ->icon('heroicon-o-paper-clip')
            ->iconButton()
            ->color('gray')
            ->disabled(count($this->files) >= 1)
            ->badge(count($this->files))
            ->modalSubmitActionLabel('Upload')
            ->form([
                FileUpload::make('attachment')
                    ->acceptedFileTypes(['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->storeFiles(false)
                    ->maxSize(256)
                    ->required(),
            ])
            ->action(function (array $data) {
                /** @var TemporaryUploadedFile $attachment */
                $attachment = $data['attachment'];

                $this->files[] = [
                    'temporaryUrl' => $attachment->temporaryUrl(),
                    'mimeType' => $attachment->getMimeType(),
                    'name' => $attachment->getClientOriginalName(),
                ];
            });
    }
}
