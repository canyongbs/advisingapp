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

namespace AdvisingApp\Ai\Jobs;

use AdvisingApp\Ai\DataTransferObjects\LlamaParseV2ParseJobData;
use AdvisingApp\Ai\Enums\LlamaParseV2JobStatus;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use AdvisingApp\Ai\Services\LlamaParse\LlamaParseV2Client;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\Multitenancy\Jobs\TenantAware;
use Throwable;

class FetchAiLinkParsingResults implements ShouldQueue, TenantAware, ShouldBeUnique
{
    use Queueable;

    public int $timeout = 120;

    public int $uniqueFor = 900;

    public function __construct(
        protected CustomerAdvisorLink|AiAssistantLink $link,
        protected string $parsingJobId,
    ) {}

    public function handle(LlamaParseV2Client $llamaParseV2Client): void
    {
        // The link may have finished, failed, or started another parsing job since this was dispatched.
        if ($this->link->parsing_job_id !== $this->parsingJobId) {
            return;
        }

        $this->link->parsing_last_checked_at = now();
        $this->link->saveParsingState();

        try {
            $parseJob = $llamaParseV2Client->getParseJob($this->parsingJobId, ['markdown_full']);
        } catch (Throwable $exception) {
            report($exception);

            $this->giveUpIfTimedOut();

            return;
        }

        if ($parseJob->status === LlamaParseV2JobStatus::Completed) {
            $this->storeParsingResults($parseJob);

            return;
        }

        if (in_array($parseJob->status, [LlamaParseV2JobStatus::Failed, LlamaParseV2JobStatus::Cancelled], strict: true)) {
            $this->link->markParsingFailed("LlamaParse reported the parse job [{$this->parsingJobId}] as {$parseJob->status->value}: " . ($parseJob->errorMessage ?? 'no error message was given.'));

            return;
        }

        $this->giveUpIfTimedOut();
    }

    public function uniqueId(): string
    {
        return $this->link->getKey();
    }

    protected function storeParsingResults(LlamaParseV2ParseJobData $parseJob): void
    {
        if (blank($parseJob->markdownFull)) {
            $this->link->markParsingFailed("LlamaParse completed the parse job [{$this->parsingJobId}] without any content.");

            return;
        }

        $this->link->parsing_results = $parseJob->markdownFull;
        $this->link->parsing_job_id = null;
        $this->link->parsing_failed_at = null;
        // Updates `updated_at`, so the vector store picks up the new parsing results.
        $this->link->save();
    }

    protected function giveUpIfTimedOut(): void
    {
        if ($this->link->parsing_started_at?->isAfter(now()->subHours(2))) {
            return;
        }

        $this->link->markParsingFailed("LlamaParse did not complete the parse job [{$this->parsingJobId}] within 2 hours.");
    }
}
