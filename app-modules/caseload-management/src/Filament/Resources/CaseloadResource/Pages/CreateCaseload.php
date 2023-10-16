<?php

namespace Assist\CaseloadManagement\Filament\Resources\CaseloadResource\Pages;

use Iterator;
use App\Models\User;
use App\Models\Import;
use App\Jobs\ImportCsv;
use Filament\Forms\Get;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Support\ChunkIterator;
use Illuminate\Support\Facades\Bus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\FileUpload;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Concerns\InteractsWithTable;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Notifications\Actions\Action as NotificationAction;
use Assist\CaseloadManagement\Filament\Resources\CaseloadResource;

class CreateCaseload extends CreateRecord implements HasTable
{
    use InteractsWithTable;
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = CaseloadResource::class;

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
            Step::make('Model')
                ->schema([
                    Select::make('model')
                        ->label('Population')
                        ->options(CaseloadModel::class)
                        ->required()
                        ->default(CaseloadModel::default())
                        ->selectablePlaceholder(false)
                        ->afterStateUpdated(function () {
                            $this->cacheForms();
                            $this->bootedInteractsWithTable();
                            $this->resetTableFiltersForm();
                        }),
                ])
                ->columns(2),
            Step::make('Type')
                ->schema([
                    Select::make('type')
                        ->options(CaseloadType::class)
                        ->default(CaseloadType::default())
                        ->selectablePlaceholder(false)
                        ->required(),
                ])
                ->columns(2),
            Step::make('Population')
                ->schema([
                    Placeholder::make('table')
                        ->content(fn (): Htmlable => $this->table)
                        ->hiddenLabel()
                        ->visible(fn (Get $get): bool => CaseloadType::tryFromCaseOrValue($get('type')) === CaseloadType::Dynamic),
                    FileUpload::make('file')
                        ->acceptedFileTypes(['text/csv', 'text/plain'])
                        ->storeFiles(false)
                        ->visibility('private')
                        ->required()
                        ->hiddenLabel()
                        ->visible(fn (Get $get): bool => CaseloadType::tryFromCaseOrValue($get('type')) === CaseloadType::Static)
                        ->helperText(fn (Get $get): string => match (CaseloadModel::tryFromCaseOrValue($get('model'))) {
                            CaseloadModel::Student => 'Upload a file of Student IDs or Other IDs, with each on a new line.',
                            CaseloadModel::Prospect => 'Upload a file of prospect email addresses, with each on a new line.',
                        }),
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns(CaseloadResource::columns($this->data['model']))
            ->filters(CaseloadResource::filters($this->data['model']), layout: FiltersLayout::AboveContent)
            // ->actions(CaseloadResource::actions($this->data['model']))
            ->query(fn () => $this->data['model']->query());
    }

    public function afterCreate(): void
    {
        if (CaseloadType::tryFromCaseOrValue($this->data['type']) === CaseloadType::Dynamic) {
            return;
        }

        /** @var TemporaryUploadedFile $file */
        $file = Arr::first($this->data['file']);

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
        $import->importer = CaseloadModel::tryFromCaseOrValue($this->data['model'])->getSubjectImporter();
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
                    'caseload_id' => $this->getRecord()->getKey(),
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
                                ->url(route('imports.failed-rows.download', ['import' => $import])),
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (CaseloadType::tryFromCaseOrValue($this->data['type']) === CaseloadType::Dynamic) {
            $data['filters'] = $this->tableFilters ?? [];
        } else {
            $data['filters'] = [];
        }

        return $data;
    }
}
