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

namespace AdvisingApp\StudentDataModel\Filament\Actions;

use AdvisingApp\StudentDataModel\Actions\CompleteStudentDataImport;
use AdvisingApp\StudentDataModel\Actions\CreateTemporaryStudentDataImportTables;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentAddressImporter;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentEmailAddressImporter;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentEnrollmentImporter;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentImporter;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentPhoneNumberImporter;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentProgramImporter;
use AdvisingApp\StudentDataModel\Jobs\PrepareStudentDataCsvImport;
use AdvisingApp\StudentDataModel\Models\Enrollment;
use AdvisingApp\StudentDataModel\Models\Program;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentDataImport;
use App\Models\Import;
use Filament\Actions\Imports\Events\ImportStarted;
use Filament\Actions\Imports\ImportColumn;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use League\Csv\ByteSequence;
use League\Csv\Reader as CsvReader;
use League\Csv\Writer;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportStudentDataAction
{
    public static function make(): ImportAction
    {
        $action = ImportAction::make();

        $makeFileUpload = fn (string $name = 'file', string $columnMapStatePath = 'columnMap', ?string $importer = null, bool $isRequired = true): FileUpload => FileUpload::make($name)
            ->label(__('filament-actions::import.modal.form.file.label'))
            ->placeholder('Upload a students CSV file')
            ->acceptedFileTypes(['text/csv', 'text/x-csv', 'application/csv', 'application/x-csv', 'text/comma-separated-values', 'text/x-comma-separated-values', 'text/plain', 'application/vnd.ms-excel'])
            ->rules($action->getFileValidationRules())
            ->afterStateUpdated(function (FileUpload $component, Component $livewire, Forms\Set $set, ?TemporaryUploadedFile $state) use ($action, $columnMapStatePath, $importer) {
                if (! $state instanceof TemporaryUploadedFile) {
                    return;
                }

                try {
                    $livewire->validateOnly($component->getStatePath());
                } catch (ValidationException $exception) {
                    $component->state([]);

                    throw $exception;
                }

                $csvStream = $action->getUploadedFileStream($state);

                if (! $csvStream) {
                    return;
                }

                $csvReader = CsvReader::createFromStream($csvStream);

                if (filled($csvDelimiter = $action->getCsvDelimiter($csvReader))) {
                    $csvReader->setDelimiter($csvDelimiter);
                }

                $csvReader->setHeaderOffset($action->getHeaderOffset() ?? 0);

                $csvColumns = $csvReader->getHeader();

                $lowercaseCsvColumnValues = array_map(Str::lower(...), $csvColumns);
                $lowercaseCsvColumnKeys = array_combine(
                    $lowercaseCsvColumnValues,
                    $csvColumns,
                );

                $set($columnMapStatePath, array_reduce(($importer ?? $action->getImporter())::getColumns(), function (array $carry, ImportColumn $column) use ($lowercaseCsvColumnKeys, $lowercaseCsvColumnValues) {
                    $carry[$column->getName()] = $lowercaseCsvColumnKeys[Arr::first(
                        array_intersect(
                            $lowercaseCsvColumnValues,
                            $column->getGuesses(),
                        ),
                    )] ?? null;

                    return $carry;
                }, []));
            })
            ->storeFiles(false)
            ->visibility('private')
            ->required($isRequired)
            ->hiddenLabel();

        $makeColumnMapper = fn (string $name = 'columnMap', ?string $fileStatePath = 'file', ?string $importer = null): Fieldset => Fieldset::make(__('filament-actions::import.modal.form.columns.label'))
            ->columns(1)
            ->inlineLabel()
            ->schema(function (Forms\Get $get) use ($action, $fileStatePath, $importer): array {
                $csvFile = Arr::first((array) ($get($fileStatePath) ?? []));

                if (! $csvFile instanceof TemporaryUploadedFile) {
                    return [];
                }

                $csvStream = $action->getUploadedFileStream($csvFile);

                if (! $csvStream) {
                    return [];
                }

                $csvReader = CsvReader::createFromStream($csvStream);

                if (filled($csvDelimiter = $action->getCsvDelimiter($csvReader))) {
                    $csvReader->setDelimiter($csvDelimiter);
                }

                $csvReader->setHeaderOffset($action->getHeaderOffset() ?? 0);

                $csvColumns = $csvReader->getHeader();
                $csvColumnOptions = array_combine($csvColumns, $csvColumns);

                return array_map(
                    fn (ImportColumn $column): Select => $column->getSelect()->options($csvColumnOptions),
                    ($importer ?? $action->getImporter())::getColumns(),
                );
            })
            ->statePath($name)
            ->visible(fn (Forms\Get $get): bool => Arr::first((array) ($get($fileStatePath) ?? [])) instanceof TemporaryUploadedFile);

        $makeDownloadAction = fn (string $name, ?string $importer): Action => Action::make($name)
            ->label(__('filament-actions::import.modal.actions.download_example.label'))
            ->link()
            ->action(function () use ($action, $importer): StreamedResponse {
                $columns = $importer::getColumns();

                $csv = Writer::createFromFileObject(new SplTempFileObject());
                $csv->setOutputBOM(ByteSequence::BOM_UTF8);

                if (filled($csvDelimiter = $action->getCsvDelimiter())) {
                    $csv->setDelimiter($csvDelimiter);
                }

                $csv->insertOne(array_map(
                    fn (ImportColumn $column): string => $column->getExampleHeader(),
                    $columns,
                ));

                $columnExamples = array_map(
                    fn (ImportColumn $column): array => $column->getExamples(),
                    $columns,
                );

                $exampleRowsCount = array_reduce(
                    $columnExamples,
                    fn (int $count, array $exampleData): int => max($count, count($exampleData)),
                    initial: 0,
                );

                $exampleRows = [];

                foreach ($columnExamples as $exampleData) {
                    for ($i = 0; $i < $exampleRowsCount; $i++) {
                        $exampleRows[$i][] = $exampleData[$i] ?? '';
                    }
                }

                $csv->insertAll($exampleRows);

                return response()->streamDownload(function () use ($csv) {
                    echo $csv->toString();
                }, __('filament-actions::import.example_csv.file_name', ['importer' => (string) str($importer)->classBasename()->kebab()]), [
                    'Content-Type' => 'text/csv',
                ]);
            });

        return $action
            ->label('Sync Records')
            ->importer(StudentImporter::class)
            ->authorize('import', Student::class)
            ->modalHeading('Synchronize Students, Programs, and Enrollments')
            ->modalDescription(fn (ImportAction $action): Htmlable => new HtmlString('WARNING: Record synchronization involves the replacement of all students, programs, and enrollments with the imported extract data. If there are existing student records that are not present in your uploaded files, those records will be removed from the system. The purpose of this tool is to synchronize and match your SIS extract data imported here. <br><br> CSV Extract Examples<br>' . collect([
                $action->getModalAction('downloadExample')?->label('Students')->toHtml(),
                $action->getModalAction('downloadEmailAddressesExample')?->label('Email addresses')->toHtml(),
                $action->getModalAction('downloadPhoneNumbersExample')?->label('Phone numbers')->toHtml(),
                $action->getModalAction('downloadAddressesExample')?->label('Addresses')->toHtml(),
                $action->getModalAction('downloadProgramsExample')?->label('Programs')->toHtml(),
                $action->getModalAction('downloadEnrollmentsExample')?->label('Enrollments')->toHtml(),
            ])->filter()->implode(' | ')))
            ->modalSubmitActionLabel('Sync Records')
            ->form(fn (ImportAction $action): array => array_merge([
                $makeFileUpload(),
                $makeFileUpload(
                    name: 'emailAddressesFile',
                    columnMapStatePath: 'emailAddressesColumnMap',
                    importer: StudentEmailAddressImporter::class,
                    isRequired: false,
                )
                    ->placeholder('Upload an email addresses CSV file'),
                $makeFileUpload(
                    name: 'phoneNumbersFile',
                    columnMapStatePath: 'phoneNumbersColumnMap',
                    importer: StudentPhoneNumberImporter::class,
                    isRequired: false,
                )
                    ->placeholder('Upload a phone numbers CSV file'),
                $makeFileUpload(
                    name: 'addressesFile',
                    columnMapStatePath: 'addressesColumnMap',
                    importer: StudentAddressImporter::class,
                    isRequired: false,
                )
                    ->placeholder('Upload an addresses CSV file'),
                $makeFileUpload(
                    name: 'programsFile',
                    columnMapStatePath: 'programsColumnMap',
                    importer: StudentProgramImporter::class,
                    isRequired: false,
                )
                    ->placeholder('Upload a programs CSV file')
                    ->visible(fn () => auth()->user()->can('import', Program::class)),
                $makeFileUpload(
                    name: 'enrollmentsFile',
                    columnMapStatePath: 'enrollmentsColumnMap',
                    importer: StudentEnrollmentImporter::class,
                    isRequired: false,
                )
                    ->placeholder('Upload a enrollments CSV file')
                    ->visible(fn () => auth()->user()->can('import', Enrollment::class)),
                $makeColumnMapper()->label('Student columns'),
                $makeColumnMapper(
                    name: 'emailAddressesColumnMap',
                    fileStatePath: 'emailAddressesFile',
                    importer: StudentEmailAddressImporter::class,
                )->label('Email address columns'),
                $makeColumnMapper(
                    name: 'phoneNumbersColumnMap',
                    fileStatePath: 'phoneNumbersFile',
                    importer: StudentPhoneNumberImporter::class,
                )->label('Phone number columns'),
                $makeColumnMapper(
                    name: 'addressesColumnMap',
                    fileStatePath: 'addressesFile',
                    importer: StudentAddressImporter::class,
                )->label('Address columns'),
                $makeColumnMapper(
                    name: 'programsColumnMap',
                    fileStatePath: 'programsFile',
                    importer: StudentProgramImporter::class,
                )->label('Program columns'),
                $makeColumnMapper(
                    name: 'enrollmentsColumnMap',
                    fileStatePath: 'enrollmentsFile',
                    importer: StudentEnrollmentImporter::class,
                )->label('Enrollment columns'),
            ], $action->getImporter()::getOptionsFormComponents()))
            ->action(function (ImportAction $action, array $data) {
                $csvFile = $data['file'];
                $emailAddressesCsvFile = $data['emailAddressesFile'] ?? null;
                $phoneNumbersCsvFile = $data['phoneNumbersFile'] ?? null;
                $addressesCsvFile = $data['addressesFile'] ?? null;
                $programsCsvFile = $data['programsFile'] ?? null;
                $enrollmentsCsvFile = $data['enrollmentsFile'] ?? null;

                $user = auth()->user();

                [$studentsImport, $emailAddressesImport, $phoneNumbersImport, $addressesImport, $programsImport, $enrollmentsImport] = DB::transaction(function () use ($action, $csvFile, $emailAddressesCsvFile, $phoneNumbersCsvFile, $addressesCsvFile, $programsCsvFile, $enrollmentsCsvFile, $user) {
                    $makeImport = function (?TemporaryUploadedFile $csvFile = null, ?string $importer = null) use ($action, $user): ?Import {
                        if (! $csvFile) {
                            return null;
                        }

                        $import = app(Import::class);
                        $import->user()->associate($user);
                        $import->file_name = $csvFile->getClientOriginalName();
                        $import->file_path = $csvFile->getRealPath();
                        $import->importer = $importer ?? $action->getImporter();
                        $import->total_rows = 0;
                        $import->save();

                        return $import;
                    };

                    return [
                        $makeImport($csvFile),
                        $makeImport($emailAddressesCsvFile, StudentEmailAddressImporter::class),
                        $makeImport($phoneNumbersCsvFile, StudentPhoneNumberImporter::class),
                        $makeImport($addressesCsvFile, StudentAddressImporter::class),
                        $makeImport($programsCsvFile, StudentProgramImporter::class),
                        $makeImport($enrollmentsCsvFile, StudentEnrollmentImporter::class),
                    ];
                });

                $import = new StudentDataImport();
                $import->user()->associate(auth()->user());
                $import->studentsImport()->associate($studentsImport);
                $import->emailAddressesImport()->associate($emailAddressesImport);
                $import->phoneNumbersImport()->associate($phoneNumbersImport);
                $import->addressesImport()->associate($addressesImport);
                $import->programsImport()->associate($programsImport);
                $import->enrollmentsImport()->associate($enrollmentsImport);
                $import->save();

                $options = array_merge(
                    $action->getOptions(),
                    Arr::except($data, [
                        'file',
                        'columnMap',
                        'emailAddressesFile',
                        'emailAddressesColumnMap',
                        'phoneNumbersFile',
                        'phoneNumbersColumnMap',
                        'addressesFile',
                        'addressesColumnMap',
                        'programsFile',
                        'programsColumnMap',
                        'enrollmentsFile',
                        'enrollmentsColumnMap',
                    ]),
                );

                // We do not want to send the loaded user relationship to the queue in job payloads,
                // in case it contains attributes that are not serializable, such as binary columns.
                $studentsImport->unsetRelation('user');
                $emailAddressesImport?->unsetRelation('user');
                $phoneNumbersImport?->unsetRelation('user');
                $addressesImport?->unsetRelation('user');
                $programsImport?->unsetRelation('user');
                $enrollmentsImport?->unsetRelation('user');

                $columnMap = $data['columnMap'];
                $emailAddressesColumnMap = $data['emailAddressesColumnMap'] ?? null;
                $phoneNumbersColumnMap = $data['phoneNumbersColumnMap'] ?? null;
                $addressesColumnMap = $data['addressesColumnMap'] ?? null;
                $programsColumnMap = $data['programsColumnMap'] ?? null;
                $enrollmentsColumnMap = $data['enrollmentsColumnMap'] ?? null;

                $importer = $studentsImport->getImporter(
                    columnMap: $columnMap,
                    options: $options,
                );

                event(new ImportStarted($studentsImport, $columnMap, $options));

                if ($emailAddressesImport) {
                    event(new ImportStarted($emailAddressesImport, $emailAddressesColumnMap, $options));
                }

                if ($phoneNumbersImport) {
                    event(new ImportStarted($phoneNumbersImport, $phoneNumbersColumnMap, $options));
                }

                if ($addressesImport) {
                    event(new ImportStarted($addressesImport, $addressesColumnMap, $options));
                }

                if ($programsImport) {
                    event(new ImportStarted($programsImport, $programsColumnMap, $options));
                }

                if ($enrollmentsImport) {
                    event(new ImportStarted($enrollmentsImport, $enrollmentsColumnMap, $options));
                }

                $jobQueue = $importer->getJobQueue();
                $jobConnection = $importer->getJobConnection();

                app(CreateTemporaryStudentDataImportTables::class)->execute($import);

                $batch = Bus::batch([
                    new PrepareStudentDataCsvImport($studentsImport, $columnMap, $options, $import),
                    ...($emailAddressesImport ? [new PrepareStudentDataCsvImport($emailAddressesImport, $emailAddressesColumnMap, $options, $import)] : []),
                    ...($phoneNumbersImport ? [new PrepareStudentDataCsvImport($phoneNumbersImport, $phoneNumbersColumnMap, $options, $import)] : []),
                    ...($addressesImport ? [new PrepareStudentDataCsvImport($addressesImport, $addressesColumnMap, $options, $import)] : []),
                    ...($programsImport ? [new PrepareStudentDataCsvImport($programsImport, $programsColumnMap, $options, $import)] : []),
                    ...($enrollmentsImport ? [new PrepareStudentDataCsvImport($enrollmentsImport, $enrollmentsColumnMap, $options, $import)] : []),
                ])
                    ->allowFailures()
                    ->when(
                        filled($jobBatchName = $importer->getJobBatchName()),
                        fn (PendingBatch $batch) => $batch->name($jobBatchName),
                    )
                    ->when(
                        filled($jobQueue),
                        fn (PendingChain $chain) => $chain->onQueue($jobQueue),
                    )
                    ->when(
                        filled($jobConnection),
                        fn (PendingChain $chain) => $chain->onConnection($jobConnection),
                    )
                    ->finally(fn () => app(CompleteStudentDataImport::class)->execute($import))
                    ->dispatch();

                $import->job_batch_id = $batch->id;
                $import->save();

                Notification::make()
                    ->title('Import started')
                    ->body('Your import has begun and will be processed in the background.')
                    ->success()
                    ->send();
            })
            ->registerModalActions([
                $makeDownloadAction('downloadEmailAddressesExample', StudentEmailAddressImporter::class),
                $makeDownloadAction('downloadPhoneNumbersExample', StudentPhoneNumberImporter::class),
                $makeDownloadAction('downloadAddressesExample', StudentAddressImporter::class),
                ...(auth()->user()->can('import', Program::class) ? [
                    $makeDownloadAction('downloadProgramsExample', StudentProgramImporter::class),
                ] : []),
                ...(auth()->user()->can('import', Enrollment::class) ? [
                    $makeDownloadAction('downloadEnrollmentsExample', StudentEnrollmentImporter::class),
                ] : []),
            ])
            ->visible(auth()->user()->can('record_sync.create'));
    }
}
