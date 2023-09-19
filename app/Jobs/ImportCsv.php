<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Import;
use App\Imports\Importer;
use Carbon\CarbonInterface;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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

        foreach ($this->rows as $row) {
            ($this->importer)($row);

            $this->import->increment('processed_rows');
        }
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
}
