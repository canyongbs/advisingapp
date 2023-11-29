<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace App\Filament\Actions;

use Closure;
use Filament\Forms;
use App\Models\User;
use App\Models\Import;
use League\Csv\Writer;
use SplTempFileObject;
use App\Jobs\ImportCsv;
use App\Imports\Importer;
use League\Csv\Statement;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use App\Support\ChunkIterator;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Bus;
use League\Csv\Reader as CsvReader;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Actions\ImportAction\ImportColumn;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Notifications\Actions\Action as NotificationAction;

class ImportAction extends Action
{
    /**
     * @var class-string<Importer>
     */
    protected string $importer;

    protected ?string $job = null;

    protected int | Closure $chunkSize = 100;

    protected int | Closure | null $maxRows = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn (ImportAction $action): string => "Import {$action->getPluralModelLabel()}");

        $this->modalDescription(fn (ImportAction $action): Htmlable => new HtmlString('<p>' . $action->getModalAction('downloadExample')->toHtml() . '</p>'));

        $this->modalSubmitActionLabel('Start import');

        $this->groupedIcon('heroicon-m-arrow-up-tray');

        $this->form(fn (ImportAction $action): array => array_merge([
            FileUpload::make('file')
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
            Fieldset::make('Columns')
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
                        fn (ImportColumn $column): Select => $column->getSelect()->options($csvColumnOptions),
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

            $totalRows = $csvResults->count();
            $maxRows = $action->getMaxRows() ?? $totalRows;

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
            $import->file_name = $csvFile->getClientOriginalName();
            $import->file_path = $csvFile->getRealPath();
            $import->importer = $action->getImporter();
            $import->total_rows = $totalRows;
            $import->save();

            $importChunkIterator = new ChunkIterator($csvResults->getRecords(), chunkSize: $action->getChunkSize());

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
                ->title($action->getSuccessNotificationTitle())
                ->body("Your import has begun and {$import->total_rows} rows will be processed in the background.")
                ->success()
                ->send();
        });

        $this->registerModalActions([
            Action::make('downloadExample')
                ->label('Download an example .csv file.')
                ->link()
                ->action(function (): StreamedResponse {
                    $columns = $this->getImporter()::getColumns();

                    $csv = Writer::createFromFileObject(new SplTempFileObject());

                    $csv->insertOne(array_map(
                        fn (ImportColumn $column): string => $column->getName(),
                        $columns,
                    ));

                    $example = array_map(
                        fn (ImportColumn $column) => $column->getExample(),
                        $columns,
                    );

                    if (array_filter(
                        $example,
                        fn ($value): bool => filled($value),
                    )) {
                        $csv->insertOne($example);
                    }

                    return response()->streamDownload(function () use ($csv) {
                        echo $csv->toString();
                    }, str($this->getImporter())->classBasename()->kebab()->append('-example.csv'), [
                        'Content-Type' => 'text/csv',
                    ]);
                }),
        ]);

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

    public function chunkSize(int | Closure $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function max(int | Closure | null $rows): static
    {
        $this->maxRows = $rows;

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

    public function getChunkSize(): int
    {
        return $this->evaluate($this->chunkSize);
    }

    public function getMaxRows(): ?int
    {
        return $this->evaluate($this->maxRows);
    }
}
