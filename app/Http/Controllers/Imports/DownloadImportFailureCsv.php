<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
