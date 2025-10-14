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

namespace AdvisingApp\Pipeline\Filament\Resources\Pipelines\Pages;

use AdvisingApp\Pipeline\Filament\Resources\Pipelines\PipelineResource;
use AdvisingApp\Pipeline\Models\Pipeline;
use AdvisingApp\Pipeline\Models\PipelineStage;
use AdvisingApp\Project\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EditPipeline extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = PipelineResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->label('Name'),
                Select::make('segment_id')
                    ->label('Group')
                    ->required()
                    ->relationship('segment', 'name')
                    ->searchable()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->preload(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->label('Description'),

                TextInput::make('default_stage')
                    ->required()
                    ->label('Default Pipeline Stage name'),

                Repeater::make('pipeline_stages')
                    ->relationship('stages')
                    ->schema([
                        TextInput::make('name')
                            ->label('Stage')
                            ->distinct()
                            ->required(),
                    ])
                    ->deleteAction(
                        function (Action $action) {
                            $action->before(function (array $arguments, Repeater $component, Action $action) {
                                $currentStage = $component->getRawItemState($arguments['item']);

                                if (isset($currentStage['id'])) {
                                    $currentStage = PipelineStage::whereHas('educatables')
                                        ->find($currentStage['id']);
                                }

                                if (isset($currentStage['id'])) {
                                    Notification::make()
                                        ->title('Error !')
                                        ->body('This stage cannot be deleted because it contains educatables!')
                                        ->danger()
                                        ->send();

                                    $action->cancel();
                                }
                            });
                        }
                    )
                    ->orderColumn('order')
                    ->reorderable()
                    ->columnSpanFull()
                    ->label('Pipeline Stages')
                    ->minItems(1)
                    ->maxItems(5),
            ]);
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $pipeline = $this->getRecord();

        assert($pipeline instanceof Pipeline);

        $project = $pipeline->project;

        $breadcrumbs = [
            ProjectResource::getUrl() => ProjectResource::getBreadcrumb(),
            ...($project ? [
                ProjectResource::getUrl('view', ['record' => $project]) => $project->name ?? '',
                ProjectResource::getUrl('manage-pipelines', ['record' => $project]) => 'Pipelines',
            ] : []),
            PipelineResource::getUrl('view', ['record' => $this->getRecord()]) => Str::limit($this->getRecordTitle(), 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        $pipeline = $this->getRecord();

        assert($pipeline instanceof Pipeline);

        $resource = static::getResource();

        $action
            ->authorize($resource::canDelete($this->getRecord()))
            ->successRedirectUrl(ProjectResource::getUrl('manage-pipelines', ['record' => $pipeline->project]));
    }
}
