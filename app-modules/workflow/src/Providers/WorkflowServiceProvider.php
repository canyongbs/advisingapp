<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Workflow\Providers;

use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowCareTeamDetails;
use AdvisingApp\Workflow\Models\WorkflowCaseDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementSmsDetails;
use AdvisingApp\Workflow\Models\WorkflowEventDetails;
use AdvisingApp\Workflow\Models\WorkflowInteractionDetails;
use AdvisingApp\Workflow\Models\WorkflowProactiveConcernDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use AdvisingApp\Workflow\Models\WorkflowStep;
use AdvisingApp\Workflow\Models\WorkflowSubscriptionDetails;
use AdvisingApp\Workflow\Models\WorkflowTagsDetails;
use AdvisingApp\Workflow\Models\WorkflowTaskDetails;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use AdvisingApp\Workflow\WorkflowPlugin;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class WorkflowServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->getId() !== 'admin' || $panel->plugin(new WorkflowPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'workflow' => Workflow::class,
            'workflow_step' => WorkflowStep::class,
            'workflow_trigger' => WorkflowTrigger::class,
            'workflow_run' => WorkflowRun::class,
            'workflow_run_step' => WorkflowRunStep::class,
            'workflow_run_step_related' => WorkflowRunStepRelated::class,
            'workflow_care_team_details' => WorkflowCareTeamDetails::class,
            'workflow_case_details' => WorkflowCaseDetails::class,
            'workflow_engagement_email_details' => WorkflowEngagementEmailDetails::class,
            'workflow_engagement_sms_details' => WorkflowEngagementSmsDetails::class,
            'workflow_event_details' => WorkflowEventDetails::class,
            'workflow_interaction_details' => WorkflowInteractionDetails::class,
            'workflow_proactive_concern_details' => WorkflowProactiveConcernDetails::class,
            'workflow_subscription_details' => WorkflowSubscriptionDetails::class,
            'workflow_tags_details' => WorkflowTagsDetails::class,
            'workflow_task_details' => WorkflowTaskDetails::class,
        ]);
    }
}
