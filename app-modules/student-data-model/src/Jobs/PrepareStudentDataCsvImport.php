<?php

namespace AdvisingApp\StudentDataModel\Jobs;

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

    public $timeout = 1140;

    public $tries = 1;

    public bool $deleteWhenMissingModels = true;

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Import $import,
        protected array $columnMap,
        protected array $options = [],
    ) {}

    public function handle(): void
    {
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
