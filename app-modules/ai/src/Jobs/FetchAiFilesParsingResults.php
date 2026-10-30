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

use AdvisingApp\Ai\Jobs\Advisors\FetchAiAssistantFileParsingResults;
use AdvisingApp\Ai\Jobs\AiAssistants\FetchAiAssistantLinkParsingResults;
use AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorFileParsingResults;
use AdvisingApp\Ai\Jobs\CustomerAdvisors\FetchCustomerAdvisorLinkParsingResults;
use AdvisingApp\Ai\Models\AiAssistantFile;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\CustomerAdvisorFile;
use AdvisingApp\Ai\Models\CustomerAdvisorLink;
use App\Features\LlamaParseLinkParsingFeature;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Foundation\Queue\Queueable;

class FetchAiFilesParsingResults implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        AiAssistantFile::query()
            ->whereNull('parsing_results')
            ->where('created_at', '>=', now()->subHour())
            ->eachById(fn (AiAssistantFile $file): PendingDispatch => dispatch(new FetchAiAssistantFileParsingResults($file)));

        CustomerAdvisorFile::query()
            ->whereNull('parsing_results')
            ->where('created_at', '>=', now()->subHour())
            ->eachById(fn (CustomerAdvisorFile $file): PendingDispatch => dispatch(new FetchCustomerAdvisorFileParsingResults($file)));

        if (! LlamaParseLinkParsingFeature::active()) {
            // TODO: Cleanup Task (llamaparse-link-parsing): delete this Jina fallback.
            CustomerAdvisorLink::query()
                ->whereNull('parsing_results')
                ->where('created_at', '>=', now()->subHour())
                ->eachById(fn (CustomerAdvisorLink $link): PendingDispatch => dispatch(new FetchCustomerAdvisorLinkParsingResults($link)));

            AiAssistantLink::query()
                ->whereNull('parsing_results')
                ->where('created_at', '>=', now()->subHour())
                ->eachById(fn (AiAssistantLink $link): PendingDispatch => dispatch(new FetchAiAssistantLinkParsingResults($link)));

            return;
        }

        foreach ([CustomerAdvisorLink::class, AiAssistantLink::class] as $linkModel) {
            $this->submitNewLinks($linkModel);
            $this->failLinksNotSubmittedInTime($linkModel);
            $this->pollInFlightLinks($linkModel);
        }
    }

    /**
     * Submits links added in the last hour that are not parsed yet, retrying a failed submission every 5 minutes.
     *
     * @param class-string<CustomerAdvisorLink|AiAssistantLink> $linkModel
     */
    protected function submitNewLinks(string $linkModel): void
    {
        $linkModel::query()
            ->whereNull('parsing_results')
            ->whereNull('parsing_job_id')
            ->whereNull('parsing_failed_at')
            ->where('created_at', '>=', now()->subHour())
            ->where(fn (Builder $query): Builder => $query
                ->whereNull('parsing_last_checked_at')
                ->orWhere('parsing_last_checked_at', '<=', now()->subMinutes(5)))
            ->eachById(fn (CustomerAdvisorLink|AiAssistantLink $link): PendingDispatch => dispatch(new SubmitAiLinkForParsing($link)));
    }

    /**
     * Gives up on links that were tried but could not be submitted within an hour of being added. Links that were
     * never tried, such as ones from before LlamaParse, are left alone.
     *
     * @param class-string<CustomerAdvisorLink|AiAssistantLink> $linkModel
     */
    protected function failLinksNotSubmittedInTime(string $linkModel): void
    {
        $linkModel::query()
            ->whereNull('parsing_results')
            ->whereNull('parsing_job_id')
            ->whereNull('parsing_failed_at')
            ->whereNotNull('parsing_last_checked_at')
            ->where('created_at', '<', now()->subHour())
            ->eachById(fn (CustomerAdvisorLink|AiAssistantLink $link) => $link->markParsingFailed('It could not be submitted to LlamaParse.'));
    }

    /**
     * Checks on parse jobs that are still in progress. Each job is first checked 5 minutes after it was submitted,
     * then every 5 minutes.
     *
     * @param class-string<CustomerAdvisorLink|AiAssistantLink> $linkModel
     */
    protected function pollInFlightLinks(string $linkModel): void
    {
        $linkModel::query()
            ->whereNotNull('parsing_job_id')
            ->where('parsing_started_at', '<=', now()->subMinutes(5))
            ->where(fn (Builder $query): Builder => $query
                ->whereNull('parsing_last_checked_at')
                ->orWhere('parsing_last_checked_at', '<=', now()->subMinutes(5)))
            ->eachById(fn (CustomerAdvisorLink|AiAssistantLink $link): PendingDispatch => dispatch(new FetchAiLinkParsingResults($link, $link->parsing_job_id)));
    }
}
