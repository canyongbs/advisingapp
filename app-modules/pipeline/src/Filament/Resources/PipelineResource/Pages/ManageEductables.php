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

namespace AdvisingApp\Pipeline\Filament\Resources\PipelineResource\Pages;

use AdvisingApp\Pipeline\Filament\Resources\PipelineResource;
use AdvisingApp\Pipeline\Models\Pipeline;
use AdvisingApp\Segment\Actions\TranslateSegmentFilters;
use AdvisingApp\Segment\Enums\SegmentModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ManageEductables extends ManageRelatedRecords implements HasTable
{
    use InteractsWithTable {
        bootedInteractsWithTable as baseBootedInteractsWithTable;
    }

    protected static string $resource = PipelineResource::class;

    public ?string $viewType = 'null';

    public ?int $segmentCount = 0;

    protected static string $relationship = 'educatablePipelineStages';

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';

    protected static string $view = 'pipeline::filament.pages.manage-pipeline-educatables';

    public function getTitle(): string
    {
        $record = $this->getRecord();

        assert($record instanceof Pipeline);

        $model = $record->segment->model;

        $label = match ($model) {
            SegmentModel::Prospect => 'Prospects',
            SegmentModel::Student => 'Students',
        };

        return "Manage {$label}";
    }

    public static function getNavigationItems(array $urlParameters = []): array
    {
        $item = parent::getNavigationItems($urlParameters)[0];

        $ownerRecord = $urlParameters['record'];

        assert($ownerRecord instanceof Pipeline);

        $model = $ownerRecord->segment->model;

        $label = match ($model) {
            SegmentModel::Prospect => 'Prospects',
            SegmentModel::Student => 'Students',
        };

        $item->label($label);

        return [$item];
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $ownerRecord = $this->getRecord();

        assert($ownerRecord instanceof Pipeline);

        $this->segmentCount = app(TranslateSegmentFilters::class)->execute($ownerRecord->segment)->count();

        if ($this->segmentCount >= 100) {
            session(['pipeline-view-type' => 'table']);
        }
        $this->viewType = session('pipeline-view-type') ?? 'table';
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $record = $this->getRecord();

        assert($record instanceof Pipeline);

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function setViewType(string $viewType): void
    {
        $this->viewType = $viewType;
        session(['pipeline-view-type' => $viewType]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        $pipeline = $this->getOwnerRecord();

        assert($pipeline instanceof Pipeline);

        $table = $pipeline->segment->model
            ->table($table);

        $table->query(fn () => app(TranslateSegmentFilters::class)->execute($pipeline->segment));

        return $table;
    }
}
