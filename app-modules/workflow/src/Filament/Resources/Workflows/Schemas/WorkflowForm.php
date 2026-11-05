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

namespace AdvisingApp\Workflow\Filament\Resources\Workflows\Schemas;

use AdvisingApp\Workflow\Filament\Forms\WorkflowTypeFormRegistry;
use AdvisingApp\Workflow\Models\Workflow;
use App\Features\WorkflowNameCitextFeature;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class WorkflowForm
{
    public static function configure(Schema $schema): Schema
    {
        $record = $schema->getRecord();

        $nameInput = TextInput::make('name')
            ->required()
            ->maxLength(255);

        // Only enforce uniqueness once the citext migration has run. Before then no unique
        // index exists and duplicate default names (e.g. "Form Workflow") are legitimate, so
        // the rule must be fully disabled to avoid blocking edits (even just toggling enabled)
        // on those pre-existing duplicates. When active, uniqueness is scoped to the owning
        // record so workflows for different objects may share a name.
        if (WorkflowNameCitextFeature::active() && $record instanceof Workflow) {
            $nameInput = $nameInput->unique(
                ignoreRecord: true,
                modifyRuleUsing: fn (Unique $rule): Unique => $rule
                    ->where('related_type', $record->related_type)
                    ->where('related_id', $record->related_id)
                    ->withoutTrashed(),
            );
        }

        $schema = $schema->components([
            $nameInput,
            Toggle::make('is_enabled')
                ->label('Enabled?')
                ->inline(false),
        ]);

        if ($record instanceof Workflow) {
            $typeFormClass = app(WorkflowTypeFormRegistry::class)
                ->for($record->workflowTrigger->related_type);

            if ($typeFormClass !== null) {
                $schema = $typeFormClass::configureForm($schema);
            }
        }

        return $schema;
    }
}
