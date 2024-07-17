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

namespace AdvisingApp\Segment\Filament\Resources\SegmentResource\Pages;

use Iterator;
use Exception;
use App\Models\User;
use App\Models\Import;
use Filament\Forms\Get;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Support\ChunkIterator;
use Filament\Forms\Components\View;
use Illuminate\Support\Facades\Bus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Forms\Components\FileUpload;
use Illuminate\Filesystem\AwsS3V3Adapter;
use AdvisingApp\Segment\Enums\SegmentType;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Segment\Enums\SegmentModel;
use Filament\Actions\Imports\Jobs\ImportCsv;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Tables\Concerns\InteractsWithTable;
use AdvisingApp\Segment\Filament\Resources\SegmentResource;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Notifications\Actions\Action as NotificationAction;

class CreateSegment extends CreateRecord implements HasTable
{
    use InteractsWithTable;
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = SegmentResource::class;

    public function getSteps(): array
    {
        return [
            Step::make('Characteristics')
                ->schema([
                    TextInput::make('name')
                        ->autocomplete(false)
                        ->string()
                        ->required(),
                    Textarea::make('description'),
                ]),
            Step::make('Population Segment Type')
                ->schema([
                    Select::make('model')
                        ->label('Population')
                        ->options(SegmentModel::class)
                        ->required()
                        ->default(SegmentModel::default())
                        ->selectablePlaceholder(false)
                        ->afterStateUpdated(function () {
                            $this->cacheForms();
                            $this->bootedInteractsWithTable();
                            $this->resetTableFiltersForm();
                        }),
                ])
                ->columns(2)
                ->visible(auth()->user()->hasLicense([Student::getLicenseType(), Prospect::getLicenseType()])),
            Step::make('Identify Population')
                ->schema([
                    Select::make('type')
                        ->options(SegmentType::class)
                        ->default(SegmentType::default())
                        ->selectablePlaceholder(false)
                        ->required(),
                ])
                ->columns(2),
            Step::make('Create Population Segment')
                ->schema([
                    View::make('filament.forms.components.table')
                        ->visible(fn (Get $get): bool => SegmentType::tryFromCaseOrValue($get('type')) === SegmentType::Dynamic),
                    FileUpload::make('file')
                        ->acceptedFileTypes(['text/csv', 'text/plain'])
                        ->storeFiles(false)
                        ->visibility('private')
                        ->required()
                        ->hiddenLabel()
                        ->visible(fn (Get $get): bool => SegmentType::tryFromCaseOrValue($get('type')) === SegmentType::Static)
                        ->helperText(fn (): string => match ($this->getSegmentModel()) {
                            SegmentModel::Student => 'Upload a file of Student IDs or Other IDs, with each on a new line.',
                            SegmentModel::Prospect => 'Upload a file of prospect email addresses, with each on a new line.',
                        }),
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        return $this->getSegmentModel()->table($table);
    }

    /**
     * @return resource | false
     */
    public function getUploadedFileStream(TemporaryUploadedFile $file)
    {
        $filePath = $file->getRealPath();

        if (config('filament.default_filesystem_disk') !== 's3') {
            return fopen($filePath, mode: 'r');
        }

        /** @var AwsS3V3Adapter $s3Adapter */
        $s3Adapter = Storage::disk('s3')->getAdapter();

        invade($s3Adapter)->client->registerStreamWrapper();

        $fileS3Path = 's3://' . config('filesystems.disks.s3.bucket') . '/' . $filePath;

        return fopen($fileS3Path, mode: 'r', context: stream_context_create([
            's3' => [
                'seekable' => true,
            ],
        ]));
    }

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();

        if (SegmentType::tryFromCaseOrValue($data['type']) === SegmentType::Dynamic) {
            return;
        }

        /** @var TemporaryUploadedFile $file */
        $file = Arr::first($data['file']);

        $fileStream = $this->getUploadedFileStream($file);

        if (! $fileStream) {
            return;
        }

        $totalRows = 0;

        while (! feof($fileStream)) {
            fgets($fileStream);

            $totalRows++;
        }

        fclose($fileStream);

        $maxRows = 100000;

        if ($maxRows < $totalRows) {
            Notification::make()
                ->title('That file is too large to import')
                ->body('You may not import more than ' . number_format($maxRows) . ' at once.')
                ->success()
                ->send();

            return;
        }

        $user = auth()->user();

        $import = new Import();
        $import->user()->associate($user);
        $import->file_name = $file->getClientOriginalName();
        $import->file_path = $file->getRealPath();
        $import->importer = $this->getSegmentModel()->getSubjectImporter();
        $import->total_rows = $totalRows;
        $import->save();

        $importChunkIterator = new ChunkIterator((function () use ($file): Iterator {
            $fileStream = $this->getUploadedFileStream($file);

            if (! $fileStream) {
                return;
            }

            while (! feof($fileStream)) {
                yield ['subject' => fgets($fileStream)];
            }

            fclose($fileStream);
        })(), chunkSize: 100);

        /** @var array<array<array<string, string>>> $importChunks */
        $importChunks = $importChunkIterator->get();

        $importJobs = collect($importChunks)
            ->map(fn (array $importChunk): object => new ImportCsv(
                $import,
                rows: $importChunk,
                columnMap: [
                    'subject' => 'subject',
                ],
                options: [
                    'segment_id' => $this->getRecord()->getKey(),
                ],
            ));

        Bus::batch($importJobs->all())
            ->allowFailures()
            ->finally(function () use ($import) {
                if ($import->failed_at) {
                    return;
                }

                $import->touch('completed_at');

                if (! $import->user instanceof User) {
                    return;
                }

                $failedRowsCount = $import->getFailedRowsCount();

                Notification::make()
                    ->title('Import completed')
                    ->body($import->importer::getCompletedNotificationBody($import))
                    ->when(
                        ! $failedRowsCount,
                        fn (Notification $notification) => $notification->success(),
                    )
                    ->when(
                        $failedRowsCount && ($failedRowsCount < $import->total_rows),
                        fn (Notification $notification) => $notification->warning(),
                    )
                    ->when(
                        $failedRowsCount === $import->total_rows,
                        fn (Notification $notification) => $notification->danger(),
                    )
                    ->when(
                        $failedRowsCount,
                        fn (Notification $notification) => $notification->actions([
                            NotificationAction::make('downloadFailedRowsCsv')
                                ->label('Download information about the failed ' . Str::plural('row', $failedRowsCount))
                                ->color('danger')
                                ->url(route('filament.imports.failed-rows.download', ['import' => $import])),
                        ]),
                    )
                    ->sendToDatabase($import->user);
            })
            ->dispatch();

        Notification::make()
            ->title('Import started')
            ->body("Your import has begun and {$import->total_rows} rows will be processed in the background.")
            ->success()
            ->send();
    }

    protected function getSegmentModel(): SegmentModel
    {
        $canAccessStudents = auth()->user()->hasLicense(Student::getLicenseType());
        $canAccessProspects = auth()->user()->hasLicense(Prospect::getLicenseType());

        return match (true) {
            $canAccessStudents && $canAccessProspects => SegmentModel::tryFromCaseOrValue($this->form->getRawState()['model']) ?? throw new Exception('Neither students nor prospects were selected.'),
            $canAccessStudents => SegmentModel::Student,
            $canAccessProspects => SegmentModel::Prospect,
            default => throw new Exception('User cannot access students or prospects.'),
        };
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['model'] = $this->getSegmentModel();

        if (SegmentType::tryFromCaseOrValue($data['type']) === SegmentType::Dynamic) {
            $data['filters'] = $this->tableFilters ?? [];
        } else {
            $data['filters'] = [];
        }

        return $data;
    }
}
