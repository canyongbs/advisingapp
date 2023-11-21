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

namespace App\Jobs;

use Exception;
use Throwable;
use App\Models\User;
use App\Models\Import;
use App\Imports\Importer;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\FailedImportRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Validation\ValidationException;

class ImportCsv implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    protected readonly Importer $importer;

    /**
     * @param array<array<string, string>> $rows
     * @param array<string, string> $columnMap
     * @param array<string, mixed> $options
     */
    public function __construct(
        readonly public Import $import,
        readonly public array $rows,
        readonly public array $columnMap,
        readonly public array $options = [],
    ) {
        $this->importer = $this->import->getImporter(
            $this->columnMap,
            $this->options,
        );
    }

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return $this->importer->getJobMiddleware();
    }

    public function handle(): void
    {
        /** @var User $user */
        $user = $this->import->user;

        auth()->login($user);

        $exceptions = [];

        foreach ($this->rows as $row) {
            try {
                DB::transaction(fn () => ($this->importer)($row));
            } catch (ValidationException $exception) {
                $this->logFailedRow($row, collect($exception->errors())->flatten()->implode(' '));
            } catch (Throwable $exception) {
                $exceptions[$exception::class] = $exception;

                $this->logFailedRow($row);
            }

            $this->import->increment('processed_rows');
        }

        $this->handleExceptions($exceptions);
    }

    public function retryUntil(): CarbonInterface
    {
        return $this->importer->getJobRetryUntil();
    }

    /**
     * @return array<int, string>
     */
    public function tags(): array
    {
        return $this->importer->getJobTags();
    }

    protected function logFailedRow(array $data, ?string $validationError = null): void
    {
        $failedRow = new FailedImportRow();
        $failedRow->import()->associate($this->import);
        $failedRow->data = $data;
        $failedRow->validation_error = $validationError;
        $failedRow->save();
    }

    protected function handleExceptions(array $exceptions): void
    {
        if (empty($exceptions)) {
            return;
        }

        if (count($exceptions) > 1) {
            throw new Exception('Multiple types of exceptions occurred: [' . implode('], [', array_keys($exceptions)) . ']');
        }

        throw Arr::first($exceptions);
    }
}
