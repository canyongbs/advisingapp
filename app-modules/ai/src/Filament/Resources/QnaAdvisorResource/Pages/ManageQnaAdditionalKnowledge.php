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

namespace AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Models\QnaAdvisorFile;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use App\Models\User;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ManageQnaAdditionalKnowledge extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = QnaAdvisorResource::class;

    protected static ?string $navigationLabel = 'Additional Knowledge';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $breadcrumb = 'Additional Knowledge';

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var QnaAdvisor $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function form(Form $form): Form
    {
        $user = auth()->guard('web')->user();

        assert($user instanceof User);

        return $form
            ->schema([
                Section::make('Additional Knowledge')
                    ->description('Add additional knowledge to this QnA Advisor to improve its responses.')
                    ->reactive()
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->schema([
                        Repeater::make('files')
                            ->relationship('files')
                            ->hiddenLabel()
                            ->when(
                                $user->isSuperAdmin(),
                                fn (Repeater $repeater) => $repeater->schema([
                                    TextInput::make('name')
                                        ->disabled(),
                                    Textarea::make('parsing_results')
                                        ->placeholder('Not parsed yet')
                                        ->disabled()
                                        ->visible($user->isSuperAdmin()),
                                ]),
                                fn (Repeater $repeater) => $repeater->simple(
                                    TextInput::make('name')
                                        ->disabled(),
                                ),
                            )
                            ->addable(false)
                            ->visible(fn (QnaAdvisor $record): bool => $record->files->isNotEmpty())
                            ->deleteAction(
                                fn (Action $action) => $action->requiresConfirmation()
                                    ->modalHeading('Are you sure you want to delete this file?')
                                    ->modalDescription('This file will be permanently removed from this QnA Advisor, and cannot be restored.')
                            ),
                        FileUpload::make('uploaded_files')
                            ->hiddenLabel()
                            ->multiple()
                            ->reactive()
                            ->maxFiles(fn (QnaAdvisor $record): int => 5 - $record->files->count())
                            ->disabled(fn (QnaAdvisor $record): bool => $record->files->count() >= 5)
                            ->acceptedFileTypes(config('ai.supported_file_types'))
                            ->storeFiles(false)
                            ->helperText(function (QnaAdvisor $record): string {
                                if ($record->files->count() < 5) {
                                    return 'You may upload a total of 5 files to this QnA Advisor. Files must be less than 20MB.';
                                }

                                return "You've reached the maximum file upload limit of 5 for this QnA Advisor. Please delete a file if you wish to upload another.";
                            })
                            ->maxSize(20000)
                            ->columnSpan(function (Get $get) {
                                $files = $get('files');
                                $firstFile = reset($files);

                                if (! $firstFile || blank($firstFile['name'])) {
                                    return 'full';
                                }

                                return 1;
                            }),
                    ]),
            ]);
    }

    public function getRedirectUrl(): ?string
    {
        return $this->getUrl(['record' => $this->getRecord()]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->fill($data);

        assert($record instanceof QnaAdvisor);

        if (filled($data['uploaded_files'] ?? null)) {
            foreach ($data['uploaded_files'] as $attachment) {
                if (! ($attachment instanceof TemporaryUploadedFile)) {
                    continue;
                }

                $file = new QnaAdvisorFile();
                $file->advisor()->associate($record);
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
                    ->post('https://api.cloud.llamaindex.ai/api/v1/parsing/upload', [
                        'invalidate_cache' => true,
                        'parse_mode' => 'parse_page_with_lvm',
                        'user_prompt' => 'If the upload has images retrieve text from it and also describe the image in detail. If the upload seems to be just an image with no text in it, just return the image description.',
                    ]);

                if ((! $response->successful()) || blank($response->json('id'))) {
                    Notification::make()
                        ->title('File Upload Failed')
                        ->body('There was an error uploading the file. Please try again later.')
                        ->danger()
                        ->send();

                    continue;
                }

                $file->file_id = $response->json('id');
                $file->save();

                $file->addMediaFromUrl($file->temporary_url)->toMediaCollection('file');
            }
        }

        $this->fillForm();

        return $record;
    }
}
