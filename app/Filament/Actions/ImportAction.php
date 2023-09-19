<?php

namespace App\Filament\Actions;

use Filament\Forms;
use App\Models\User;
use App\Models\Import;
use App\Jobs\ImportCsv;
use App\Imports\Importer;
use League\Csv\Statement;
use Illuminate\Support\Arr;
use Filament\Actions\Action;
use App\Support\ChunkIterator;
use Illuminate\Support\Facades\Bus;
use League\Csv\Reader as CsvReader;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Filesystem\AwsS3V3Adapter;
use App\Filament\Actions\ImportAction\ImportColumn;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImportAction extends Action
{
    /**
     * @var class-string<Importer>
     */
    protected string $importer;

    protected ?string $job = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn (ImportAction $action): string => "Import {$action->getPluralModelLabel()}");

        $this->modalSubmitActionLabel('Start import');

        $this->groupedIcon('heroicon-m-arrow-up-tray');

        $this->form(fn (ImportAction $action): array => array_merge([
            Forms\Components\FileUpload::make('file')
                ->placeholder('Upload a .csv file')
                ->acceptedFileTypes(['text/csv', 'text/plain'])
                ->afterStateUpdated(function (Forms\Set $set, ?TemporaryUploadedFile $state) use ($action) {
                    if (! $state instanceof TemporaryUploadedFile) {
                        return;
                    }

                    $csvStream = $this->getUploadedFileStream($state);

                    if (! $csvStream) {
                        return;
                    }

                    $csvReader = CsvReader::createFromStream($csvStream);
                    $csvReader->setHeaderOffset(0);

                    $csvColumns = $csvReader->getHeader();

                    $lowercaseCsvColumnValues = array_map('strtolower', $csvColumns);
                    $lowercaseCsvColumnKeys = array_combine(
                        $lowercaseCsvColumnValues,
                        $csvColumns,
                    );

                    $set('columnMap', array_reduce($action->getImporter()::getColumns(), function (array $carry, ImportColumn $column) use ($lowercaseCsvColumnKeys, $lowercaseCsvColumnValues) {
                        $carry[$column->getName()] = $lowercaseCsvColumnKeys[
                            Arr::first(
                                array_intersect(
                                    $lowercaseCsvColumnValues,
                                    $column->getGuesses(),
                                ),
                            )
                        ] ?? null;

                        return $carry;
                    }, []));
                })
                ->storeFiles(false)
                ->visibility('private')
                ->required()
                ->hiddenLabel(),
            Forms\Components\Fieldset::make('Columns')
                ->schema(function (Forms\Get $get) use ($action): array {
                    $csvFile = Arr::first((array) ($get('file') ?? []));

                    if (! $csvFile instanceof TemporaryUploadedFile) {
                        return [];
                    }

                    $csvStream = $this->getUploadedFileStream($csvFile);

                    if (! $csvStream) {
                        return [];
                    }

                    $csvReader = CsvReader::createFromStream($csvStream);
                    $csvReader->setHeaderOffset(0);

                    $csvColumns = $csvReader->getHeader();
                    $csvColumnOptions = array_combine($csvColumns, $csvColumns);

                    return array_map(
                        fn (ImportColumn $column): Forms\Components\Select => $column->getSelect()->options($csvColumnOptions),
                        $action->getImporter()::getColumns(),
                    );
                })
                ->statePath('columnMap')
                ->visible(fn (Forms\Get $get): bool => Arr::first((array) ($get('file') ?? [])) instanceof TemporaryUploadedFile),
        ], $action->getImporter()::getOptionsFormComponents()));

        $this->action(function (ImportAction $action, array $data) {
            /** @var TemporaryUploadedFile $csvFile */
            $csvFile = $data['file'];

            $csvStream = $this->getUploadedFileStream($csvFile);

            if (! $csvStream) {
                return;
            }

            $csvReader = CsvReader::createFromStream($csvStream);
            $csvReader->setHeaderOffset(0);
            $csvResults = Statement::create()->process($csvReader);

            $user = auth()->user();

            $import = new Import();
            $import->user()->associate($user);
            $import->file_name = $csvFile->getClientOriginalName();
            $import->file_path = $csvFile->getRealPath();
            $import->importer = $action->getImporter();
            $import->total_rows = $csvResults->count();
            $import->save();

            $importChunkIterator = new ChunkIterator($csvResults->getRecords(), chunkSize: 20);

            /** @var array<array<array<string, string>>> $importChunks */
            $importChunks = $importChunkIterator->get();

            $job = $action->getJob();

            $importJobs = collect($importChunks)
                ->map(fn (array $importChunk): object => new ($job)(
                    $import,
                    rows: $importChunk,
                    columnMap: $data['columnMap'],
                    options: Arr::except($data, ['file', 'columnMap']),
                ));

            Bus::batch($importJobs->all())
                ->allowFailures()
                ->catch(function () use ($import) {
                    $import->touch('failed_at');

                    if (! $import->user instanceof User) {
                        return;
                    }

                    Notification::make()
                        ->title('Import failed')
                        ->body($import->importer::getImportFailureNotificationBody($import->processed_rows))
                        ->danger()
                        ->sendToDatabase($import->user);
                })
                ->finally(function () use ($import) {
                    if ($import->failed_at) {
                        return;
                    }

                    $import->touch('completed_at');

                    if (! $import->user instanceof User) {
                        return;
                    }

                    Notification::make()
                        ->title('Import completed')
                        ->body($import->importer::getCompletedNotificationBody($import->total_rows))
                        ->success()
                        ->sendToDatabase($import->user);
                })
                ->dispatch();

            Notification::make()
                ->title($action->getSuccessNotificationTitle())
                ->body("Your import has begun and {$import->total_rows} rows will be processed in the background.")
                ->success()
                ->send();
        });

        $this->color('gray');

        $this->modalWidth('xl');

        $this->successNotificationTitle('Import started');

        $this->model(fn (ImportAction $action): string => $action->getImporter()::getModel());
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

        /** @phpstan-ignore-next-line */
        invade($s3Adapter)->client->registerStreamWrapper();

        $fileS3Path = 's3://' . config('filesystems.disks.s3.bucket') . '/' . $filePath;

        return fopen($fileS3Path, mode: 'r', context: stream_context_create([
            's3' => [
                'seekable' => true,
            ],
        ]));
    }

    public static function getDefaultName(): ?string
    {
        return 'import';
    }

    /**
     * @param class-string<Importer> $importer
     */
    public function importer(string $importer): static
    {
        $this->importer = $importer;

        return $this;
    }

    /**
     * @param class-string | null $job
     */
    public function job(?string $job): static
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return class-string<Importer>
     */
    public function getImporter(): string
    {
        return $this->importer;
    }

    /**
     * @return class-string
     */
    public function getJob(): string
    {
        return $this->job ?? ImportCsv::class;
    }
}
