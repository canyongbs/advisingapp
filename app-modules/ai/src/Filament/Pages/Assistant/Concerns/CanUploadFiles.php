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

use AdvisingApp\Ai\Models\AiMessageFile;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use App\Features\LlamaParse;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * @property-read bool $isParsingFiles
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

    public function checkForParsingResults(string $key): void
    {
        if (! in_array($key, $this->files)) {
            return;
        }

        $file = AiMessageFile::find($key);

        if (! $file) {
            return;
        }

        if (filled($file->parsing_results)) {
            return;
        }

        $response = Http::withToken(app(AiIntegrationsSettings::class)->llamaparse_api_key)
            ->get("https://api.cloud.llamaindex.ai/api/v1/parsing/job/{$file->file_id}/result/text");

        if ((! $response->successful()) || blank($response->json('text'))) {
            return;
        }

        $file->parsing_results = $response->json('text');
        $file->save();
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
    public function isParsingFiles(): bool
    {
        if (! LlamaParse::active()) {
            return false;
        }

        return AiMessageFile::query()
            ->whereKey($this->files)
            ->whereNull('parsing_results')
            ->exists();
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

                /** @var AwsS3V3Adapter $s3Adapter */
                $s3Adapter = Storage::disk('s3')->getAdapter();

                invade($s3Adapter)->client->registerStreamWrapper(); /** @phpstan-ignore-line */
                $fileS3Path = (string) str('s3://' . config('filesystems.disks.s3.bucket') . '/' . $attachment->getRealPath())->replace('\\', '/');

                $resource = fopen($fileS3Path, mode: 'r', context: stream_context_create([
                    's3' => [
                        'seekable' => true,
                    ],
                ]));

                $response = Http::attach(
                    'file',
                    $resource,
                    $file->name,
                    ['Content-Type' => $file->mime_type]
                )
                    ->withToken(app(AiIntegrationsSettings::class)->llamaparse_api_key)
                    ->acceptJson()
                    ->post('https://api.cloud.llamaindex.ai/api/v1/parsing/upload');

                if ((! $response->successful()) || blank($response->json('id'))) {
                    Notification::make()
                        ->title('File Upload Failed')
                        ->body('There was an error uploading the file. Please try again later.')
                        ->danger()
                        ->send();

                    $action->halt();

                    return;
                }

                $file->file_id = $response->json('id');
                $file->save();

                $file->addMediaFromUrl($file->temporary_url)->toMediaCollection('files');

                $this->files[] = $file->getKey();
            });
    }
}
