<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Overrides\Laravel;

use Illuminate\Bus\DatabaseBatchRepository;
use Illuminate\Bus\UpdatedBatchJobCounts;

/**
 * Postgres-native counter updates for a batch's job_batches row.
 *
 * Every job completing in a batch updates the same row, and the framework does each update as a SELECT ... FOR UPDATE
 * transaction that holds the row lock across two client round trips. With many concurrent workers on one batch those
 * locks queue up and stall completions. Each override here is a single UPDATE ... RETURNING statement instead, so the
 * lock is only held while Postgres applies it. Exactly one completion still sees pending_jobs reach zero, and the
 * jsonb expressions keep the framework's failed_job_ids bookkeeping.
 *
 * Permanent: proposed upstream as https://github.com/laravel/framework/pull/61461 and rejected.
 */
class PostgresBatchRepository extends DatabaseBatchRepository
{
    /**
     * @param  string  $batchId
     * @param  string  $jobId
     *
     * @return UpdatedBatchJobCounts
     */
    public function decrementPendingJobs($batchId, $jobId)
    {
        $counts = $this->connection->selectOne(
            'update ' . $this->wrappedTable() . ' set
                "pending_jobs" = "pending_jobs" - 1,
                "failed_job_ids" = (coalesce("failed_job_ids", \'[]\')::jsonb - ?::text)::text
            where "id" = ?
            returning "pending_jobs", "failed_jobs"',
            [$jobId, $batchId],
            false,
        );

        return $this->countsFrom($counts);
    }

    /**
     * @param  string  $batchId
     * @param  string  $jobId
     *
     * @return UpdatedBatchJobCounts
     */
    public function incrementFailedJobs($batchId, $jobId)
    {
        $counts = $this->connection->selectOne(
            'update ' . $this->wrappedTable() . ' set
                "failed_jobs" = "failed_jobs" + 1,
                "failed_job_ids" = case
                    when jsonb_exists(coalesce("failed_job_ids", \'[]\')::jsonb, ?::text) then "failed_job_ids"
                    else (coalesce("failed_job_ids", \'[]\')::jsonb || to_jsonb(?::text))::text
                end
            where "id" = ?
            returning "pending_jobs", "failed_jobs"',
            [$jobId, $jobId, $batchId],
            false,
        );

        return $this->countsFrom($counts);
    }

    private function countsFrom(mixed $counts): UpdatedBatchJobCounts
    {
        if ($counts === null) {
            return new UpdatedBatchJobCounts(0, 0);
        }

        assert(is_object($counts) && is_numeric($counts->pending_jobs) && is_numeric($counts->failed_jobs));

        return new UpdatedBatchJobCounts((int) $counts->pending_jobs, (int) $counts->failed_jobs);
    }

    private function wrappedTable(): string
    {
        return $this->connection->getQueryGrammar()->wrapTable($this->table);
    }
}
