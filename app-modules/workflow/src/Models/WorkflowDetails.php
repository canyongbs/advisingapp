<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Workflow\Models;

use AdvisingApp\Workflow\Filament\Blocks\CareTeamBlock;
use AdvisingApp\Workflow\Filament\Blocks\CaseBlock;
use AdvisingApp\Workflow\Filament\Blocks\EngagementEmailBlock;
use AdvisingApp\Workflow\Filament\Blocks\EngagementSmsBlock;
use AdvisingApp\Workflow\Filament\Blocks\EventBlock;
use AdvisingApp\Workflow\Filament\Blocks\InteractionBlock;
use AdvisingApp\Workflow\Filament\Blocks\ProactiveAlertBlock;
use AdvisingApp\Workflow\Filament\Blocks\SubscriptionBlock;
use AdvisingApp\Workflow\Filament\Blocks\TagsBlock;
use AdvisingApp\Workflow\Filament\Blocks\TaskBlock;
use AdvisingApp\Workflow\Filament\Blocks\WorkflowActionBlock;
use AdvisingApp\Workflow\Jobs\ExecuteWorkflowActionJob;
use App\Models\BaseModel;
use App\Settings\LicenseSettings;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class WorkflowDetails extends BaseModel
{
    abstract public function getLabel(): string;

    abstract public function getBlock(): WorkflowActionBlock;

    abstract public function getActionExecutableJob(WorkflowRunStep $workflowRunStep): ExecuteWorkflowActionJob;

    /**
     * @return array<int, WorkflowActionBlock>
     */
    public static function blocks(): array
    {
        $blocks = [
            //            CareTeamBlock::make(),
            //            EngagementEmailBlock::make(),
            EngagementSmsBlock::make(),
            //            InteractionBlock::make(),
            //            ProactiveAlertBlock::make(),
            //            SubscriptionBlock::make(),
            //            TagsBlock::make(),
            //            TaskBlock::make(),
        ];

        if (app(LicenseSettings::class)->data->addons->caseManagement) {
            $blocks[] = CaseBlock::make();
        }

        //        if (app(LicenseSettings::class)->data->addons->eventManagement) {
        //            $blocks[] = EventBlock::make();
        //        }

        return $blocks;
    }

    /**
     * @return array<int, covariant Field>
     */
    public function getEditFields(): array
    {
        return $this->getBlock()->editFields();
    }

    /**
     * @return BelongsTo<WorkflowStep, $this>
     */
    public function workflowStep(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'current_details_id');
    }
}
