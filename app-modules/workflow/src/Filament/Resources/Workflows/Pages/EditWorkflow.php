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

namespace AdvisingApp\Workflow\Filament\Resources\Workflows\Pages;

use AdvisingApp\Application\Filament\Resources\ApplicationResource;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Workflow\Filament\Resources\Workflows\WorkflowResource;
use AdvisingApp\Workflow\Models\Workflow;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWorkflow extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = WorkflowResource::class;

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $record = $this->getRecord();

        assert($record instanceof Workflow);

        return match ($record->workflowTrigger->related_type) {
            'form' => [
                FormResource::getUrl() => FormResource::getBreadcrumb(),
                FormResource::getUrl('edit', [$record->workflowTrigger->related_id]) => FormResource::getRecordTitle(Form::find($record->workflowTrigger->related_id)),
                $resource::getUrl() => $resource::getBreadcrumb(),
            ],
            'application' => [
                ApplicationResource::getUrl() => ApplicationResource::getBreadcrumb(),
                ApplicationResource::getUrl('edit', [$record->workflowTrigger->related_id]) => ApplicationResource::getRecordTitle(Application::find($record->workflowTrigger->related_id)),
                $resource::getUrl() => $resource::getBreadcrumb(),
            ],
            default => [$resource::getUrl() => $resource::getBreadcrumb()]
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successRedirectUrl(function (Workflow $record) {
                    return match ($record->workflowTrigger->related_type) {
                        Form::class => FormResource::getUrl('edit', [$record->workflowTrigger->related_id]),
                        default => route('filament.admin.pages.dashboard'),
                    };
                }),
        ];
    }
}
