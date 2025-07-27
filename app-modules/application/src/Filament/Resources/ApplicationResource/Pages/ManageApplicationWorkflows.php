<?php

namespace AdvisingApp\Application\Filament\Resources\ApplicationResource\Pages;

use AdvisingApp\Application\Filament\Resources\ApplicationResource;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Filament\Resources\WorkflowResource;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Features\WorkflowFeature;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Throwable;

class ManageApplicationWorkflows extends ManageRelatedRecords
{
    protected static string $resource = ApplicationResource::class;

    protected static string $relationship = 'workflows';

    public static function canAccess(array $parameters = []): bool
    {
        return WorkflowFeature::active() && parent::canAccess($parameters);
    }

    public static function getNavigationLabel(): string
    {
        return 'Workflows';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('name'),
                IconColumn::make('is_enabled')
                    ->label('Enabled')
                    ->boolean(),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->recordUrl(fn (Workflow $record) => WorkflowResource::getUrl('edit', [$record]));
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Create New Workflow')
                ->action(function () {
                    try{
                        DB::beginTransaction();

                        $workflowTrigger = new WorkflowTrigger(['type' => WorkflowTriggerType::EventBased,]);

                        $workflowTrigger->related()->associate($this->getOwnerRecord());
                        $workflowTrigger->createdBy()->associate(auth()->user());

                        $workflowTrigger->save();

                        $workflow = new Workflow([
                            'name' => 'Application Workflow',
                            'is_enabled' => false,
                        ]);

                        $workflow->workflowTrigger()->associate($workflowTrigger);

                        $workflow->save();

                        DB::commit();
                    } catch (Throwable $throw) {
                        DB::rollBack();

                        throw $throw;
                    }

                    redirect(WorkflowResource::getUrl('edit', [$workflow]));
                }),
        ];
    }
}