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

use AdvisingApp\Ai\Jobs\CustomerAdvisors\AutomaticallyEndCustomerAdvisors;
use AdvisingApp\Ai\Jobs\FetchAiFilesParsingResults;
use AdvisingApp\Campaign\Jobs\ExecuteCampaignActions;
use AdvisingApp\Engagement\Jobs\DeliverEngagements;
use AdvisingApp\Engagement\Jobs\GatherAndDispatchSesS3InboundEmails;
use AdvisingApp\IntegrationAwsSesEventHandling\Jobs\ConsumeSesEventsFromSqs;
use AdvisingApp\StudentDataModel\Jobs\LookupPhoneNumber;
use AdvisingApp\Workflow\Jobs\ExecuteWorkflowActionStepsJob;
use App\Jobs\LoadTestJob;
use App\Jobs\RunHealthChecks;

/*
|--------------------------------------------------------------------------
| Sentry trace sampling
|--------------------------------------------------------------------------
|
| Read by App\Support\Sentry\TracesSampler, which decides per transaction
| what share of requests and jobs Sentry traces. Sentry bills per span, so
| rare, slow work is traced in full and frequent, routine work sparsely.
|
*/
return [
    'rates' => [
        // Jobs declaring a timeout of at least `long_job_timeout_seconds`, such as AI, research and import jobs.
        'long_jobs' => (float) env('SENTRY_TRACES_LONG_JOB_RATE', 1.0),
        'high_volume_jobs' => (float) env('SENTRY_TRACES_HIGH_VOLUME_JOB_RATE', 0.01),
        'jobs' => (float) env('SENTRY_TRACES_JOB_RATE', 0.1),
        'requests' => (float) env('SENTRY_TRACES_REQUEST_RATE', 0.05),
        'livewire_updates' => (float) env('SENTRY_TRACES_LIVEWIRE_UPDATE_RATE', 0.01),
    ],

    'long_job_timeout_seconds' => 600,

    // Run every minute (or per phone number), so even 1% gives plenty of samples. Every `DispatchForEachTenant`
    // orchestrator counts as one too.
    'high_volume_jobs' => [
        AutomaticallyEndCustomerAdvisors::class,
        ConsumeSesEventsFromSqs::class,
        DeliverEngagements::class,
        ExecuteCampaignActions::class,
        ExecuteWorkflowActionStepsJob::class,
        FetchAiFilesParsingResults::class,
        GatherAndDispatchSesS3InboundEmails::class,
        LookupPhoneNumber::class,
        RunHealthChecks::class,
    ],

    'untraced_jobs' => [
        LoadTestJob::class,
    ],

    'untraced_paths' => [
        '/up',
        '/api/health',
    ],
];
