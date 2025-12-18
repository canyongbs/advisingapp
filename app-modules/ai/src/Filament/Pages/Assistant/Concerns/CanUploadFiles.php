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

namespace AdvisingApp\Ai\Filament\Pages\Assistant\Concerns;

use AdvisingApp\Ai\Actions\FetchFileParsingResults;
use AdvisingApp\Ai\Actions\UploadFileForParsing;
use AdvisingApp\Ai\Models\AiMessageFile;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * @property-read bool $isProcessingFiles
 */
trait CanUploadFiles
{
    /**
     * @var array<string>
     */
    #[Locked]
    public array $files = [];

    public function removeUploadedFile(string $key): void
    {
        $this->files = array_filter($this->files, fn (string $file): bool => $file !== $key);
    }

    public function clearFiles(): void
    {
        $this->files = [];
    }

    /**
     * @return array<AiMessageFile>
     */
    public function getFiles(): array
    {
        return AiMessageFile::query()
            ->whereKey($this->files)
            ->get()
            ->all();
    }

    #[Computed]
    public function isProcessingFiles(): bool
    {
        $files = $this->getFiles();

        $hasFilesWithoutParsingResults = false;

        foreach ($files as $file) {
            if (filled($file->parsing_results)) {
                continue;
            }

            $result = app(FetchFileParsingResults::class)->execute($file->file_id, $file->mime_type);

            if (blank($result->parsingResults)) {
                $hasFilesWithoutParsingResults = true;

                continue;
            }

            $file->parsing_results = $result->parsingResults;
            $file->save();
        }

        $threadFiles = [
            ...AiMessageFile::query()
                ->whereNotNull('parsing_results')
                ->whereHas(
                    'message',
                    fn (Builder $query) => $query->whereBelongsTo($this->thread, 'thread'),
                )
                ->get()
                ->all(),
            ...array_filter(
                $files,
                fn (AiMessageFile $file): bool => filled($file->parsing_results),
            ),
        ];

        $areParsedFilesReady = $this->thread?->assistant?->model->getService()->areFilesReady($threadFiles) ?? true;

        if ($hasFilesWithoutParsingResults) {
            return true;
        }

        return ! $areParsedFilesReady;
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
            ->badgeColor('primary')
            ->modalSubmitActionLabel('Upload')
            ->schema([
                FileUpload::make('attachment')
                    ->acceptedFileTypes(config('ai.supported_file_types'))
                    ->storeFiles(false)
                    ->helperText('The maximum file size is 20MB.')
                    ->maxSize(20000)
                    ->required(),
            ])
            ->action(function (Action $action, array $data) {
                /** @var TemporaryUploadedFile $attachment */
                $attachment = $data['attachment'];

                $file = new AiMessageFile();
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

                    $action->halt();

                    return;
                }

                $file->file_id = $result->fileId;
                $file->parsing_results = $result->parsingResults;
                $file->save();

                $file->addMediaFromUrl($file->temporary_url)->toMediaCollection('files');

                $this->files[] = $file->getKey();
            });
    }
}
