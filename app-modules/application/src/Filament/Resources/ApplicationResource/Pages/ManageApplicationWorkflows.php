<?php

namespace AdvisingApp\Application\Filament\Resources\ApplicationResource\Pages;

use AdvisingApp\Application\Filament\Resources\ApplicationResource;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Filament\Resources\WorkflowResource;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Features\WorkflowFeature;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Throwable;

class ManageApplicationWorkflows extends ManageRelatedRecords
{
    protected static string $resource = ApplicationResource::class;

    protected static string $relationship = 'workflows';

    // public static function canAccess(array $arguments = []): bool
    // {
    //     return WorkflowFeature::active() && parent::canAccess($arguments);
    // }

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
                    ->state(fn (Workflow $record) => $record->is_enabled)
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
                    $user = auth()->user();

                    assert($user instanceof User);

                    try{
                        DB::beginTransaction();

                        $workflowTrigger = new WorkflowTrigger([
                            'type' => WorkflowTriggerType::EventBased,
                        ]);

                        $workflowTrigger->related()->associate($this->getOwnerRecord());
                        $workflowTrigger->createdBy()->associate($user);

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
                })
        ];
    }
}