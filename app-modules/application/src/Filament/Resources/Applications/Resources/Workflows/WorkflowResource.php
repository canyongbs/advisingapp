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

namespace AdvisingApp\Application\Filament\Resources\Applications\Resources\Workflows;

use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Filament\Resources\Applications\Resources\Workflows\Pages\EditWorkflow;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Workflow\Filament\Resources\Workflows\RelationManagers\WorkflowStepsRelationManager;
use AdvisingApp\Workflow\Filament\Resources\Workflows\Schemas\WorkflowForm;
use AdvisingApp\Workflow\Models\Workflow;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WorkflowResource extends Resource
{
    protected static ?string $model = Workflow::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'workflows';

    protected static ?string $breadcrumb = 'Manage Application Workflows';

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return ApplicationResource::asParent(childResource: self::class)
            ->relationship('workflows')
            ->inverseRelationship('application');
    }

    public static function form(Schema $schema): Schema
    {
        return WorkflowForm::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            'workflowSteps' => WorkflowStepsRelationManager::class,
        ];
    }

    /**
     * @param Builder<Workflow> $query
     *
     * @return Builder<Workflow>
     */
    public static function scopeEloquentQueryToParent(Builder $query, Model $parentRecord): Builder
    {
        assert($parentRecord instanceof Application);

        return $query->whereHas(
            'workflowTrigger',
            fn (Builder $query): Builder => $query->whereMorphedTo('related', $parentRecord),
        );
    }

    public static function getPages(): array
    {
        return [
            'edit' => EditWorkflow::route('/{record}/edit'),
        ];
    }

    public static function getIndexUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null, bool $shouldGuessMissingParameters = false): string
    {
        $record = $parameters['application'] ?? null;
        unset($parameters['application']);

        return ApplicationResource::getUrl('manage-application-workflows', [
            ...$parameters,
            'record' => $record,
        ], $isAbsolute, $panel, $tenant, $shouldGuessMissingParameters);
    }
}
