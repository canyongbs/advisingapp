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
