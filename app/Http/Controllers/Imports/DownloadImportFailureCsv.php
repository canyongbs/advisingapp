<?php

namespace App\Http\Controllers\Imports;

use App\Models\Import;
use League\Csv\Writer;
use SplTempFileObject;
use App\Models\FailedImportRow;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadImportFailureCsv
{
    public function __invoke(Import $import): StreamedResponse
    {
        abort_unless($import->user->is(auth()->user()), 403);

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        $columnHeaders = array_keys($import->failedRows()->first()->data);
        $columnHeaders[] = 'error';

        $csv->insertOne($columnHeaders);

        $import->failedRows()
            ->lazyById(100)
            ->each(fn (FailedImportRow $failedImportRow) => $csv->insertOne([
                ...$failedImportRow->data,
                'error' => $failedImportRow->validation_error ?? 'System error, please contact support.',
            ]));

        return response()->streamDownload(function () use ($csv) {
            foreach ($csv->chunk(1000) as $offset => $chunk) {
                echo $chunk;

                if ($offset % 1000) {
                    flush();
                }
            }
        }, "import-{$import->id}-{$import->file_name}-failed-rows.csv", [
            'Content-Type' => 'text/csv',
        ]);
    }
}
