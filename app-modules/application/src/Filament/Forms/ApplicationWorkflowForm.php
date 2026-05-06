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

namespace AdvisingApp\Application\Filament\Forms;

use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Workflow\Enums\WorkflowTriggerEvent;
use AdvisingApp\Workflow\Filament\Forms\WorkflowTypeForm;
use App\Features\AdmissionsStageWorkflowTriggersFeature;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ApplicationWorkflowForm extends WorkflowTypeForm
{
    public static function relatedType(): string
    {
        return (new Application())->getMorphClass();
    }

    public static function configureForm(Schema $schema): Schema
    {
        // TODO: Cleanup Task - Once AdmissionsStageWorkflowTriggersFeature is removed,
        // drop the AdmissionsStageWorkflowTriggersFeature::active() check from both visible()
        // callbacks below — keep only the related_type check (which is what gates these fields
        // to application workflows specifically).
        return $schema
            ->components([
                ...$schema->getComponents(),
                Hidden::make('workflowTrigger.sub_related_type')
                    ->default((new ApplicationSubmissionState())->getMorphClass()),
                Select::make('workflowTrigger.sub_related_id')
                    ->label('Stage')
                    ->options(
                        // @phpstan-ignore method.notFound
                        fn (): array => ApplicationSubmissionState::query()
                            ->withoutArchived()
                            ->oldest('id')
                            ->pluck('name', 'id')
                            ->all(),
                    )
                    ->required()
                    ->visible(fn (): bool => AdmissionsStageWorkflowTriggersFeature::active()),
                Radio::make('workflowTrigger.event')
                    ->label('Trigger')
                    ->options(WorkflowTriggerEvent::class)
                    ->required()
                    ->inline()
                    ->inlineLabel(false)
                    ->visible(fn (): bool => AdmissionsStageWorkflowTriggersFeature::active()),
            ]);
    }
}
