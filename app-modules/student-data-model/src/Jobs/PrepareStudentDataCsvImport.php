<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\StudentDataModel\Jobs;

use AdvisingApp\StudentDataModel\Models\StudentDataImport;
use App\Models\Import;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Filament\Support\ChunkIterator;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Filesystem\AwsS3V3Adapter;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\CharsetConverter;
use League\Csv\Reader as CsvReader;
use League\Csv\Statement;

class PrepareStudentDataCsvImport implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 1140;

    public int $tries = 1;

    public bool $deleteWhenMissingModels = true;

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Import $import,
        protected array $columnMap,
        protected array $options = [],
        protected ?StudentDataImport $studentDataImport = null,
    ) {}

    public function handle(): void
    {
        $this->studentDataImport->started_at ??= now();
        $this->studentDataImport->save();

        $batch = $this->batch();

        /** @var AwsS3V3Adapter $s3Adapter */
        $s3Adapter = Storage::disk('s3')->getAdapter();

        invade($s3Adapter)->client->registerStreamWrapper();
        $fileS3Path = (string) str('s3://' . config('filesystems.disks.s3.bucket') . '/' . $this->import->file_path)->replace('\\', '/');

        $csvStream = fopen($fileS3Path, mode: 'r', context: stream_context_create([
            's3' => [
                'seekable' => true,
            ],
        ]));

        $inputEncoding = $this->detectCsvEncoding($csvStream);
        $outputEncoding = 'UTF-8';

        if (
            filled($inputEncoding) &&
            (Str::lower($inputEncoding) !== Str::lower($outputEncoding))
        ) {
            CharsetConverter::register();

            stream_filter_append(
                $csvStream,
                CharsetConverter::getFiltername($inputEncoding, $outputEncoding),
                STREAM_FILTER_READ,
            );
        }

        $csvReader = CsvReader::createFromStream($csvStream);
        $csvReader->setDelimiter(',');
        $csvReader->setHeaderOffset(0);
        $csvResults = Statement::create()->process($csvReader);

        $this->import->total_rows = $csvResults->count();
        $this->import->save();

        $importChunkIterator = new ChunkIterator($csvResults->getRecords(), chunkSize: 500);

        /** @var array<array<array<string, string>>> $importChunks */
        $importChunks = $importChunkIterator->get();

        foreach ($importChunks as $importChunk) {
            $batch->add(app(ImportCsv::class, [
                'import' => $this->import,
                'rows' => base64_encode(serialize($importChunk)),
                'columnMap' => $this->columnMap,
                'options' => $this->options,
            ]));
        }
    }

    public function getJobQueue(): ?string
    {
        return config('queue.import_export_queue');
    }

    protected function detectCsvEncoding(mixed $resource): ?string
    {
        $fileHeader = fgets($resource);

        // The encoding of a subset should be declared before the encoding of its superset.
        $encodings = [
            'UTF-8',
            'SJIS-win',
            'EUC-KR',
            'ISO-8859-1',
            'GB18030',
            'Windows-1251',
            'Windows-1252',
            'EUC-JP',
        ];

        foreach ($encodings as $encoding) {
            if (! mb_check_encoding($fileHeader, $encoding)) {
                continue;
            }

            return $encoding;
        }

        return null;
    }
}
