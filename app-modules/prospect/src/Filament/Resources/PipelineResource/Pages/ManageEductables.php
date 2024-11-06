<?php

namespace AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Prospect\Models\PipelineStage;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource;
use Illuminate\Support\Str;

class ManageEductables extends ManageRelatedRecords
{
    protected static string $resource = PipelineResource::class;

    public ?string $viewType = 'null';

    protected static string $relationship = 'prospects';

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';

    protected static ?string $title = 'Manage Pipeline Subjects';

    protected static ?string $navigationLabel = 'Pipeline Educatables';

    protected static string $view = 'prospect::filament.pages.manage-pipeline-educatables';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->viewType = session('pipeline-view-type') ?? 'table';
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var Pipeline $record */
        $record = $this->getRecord();

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
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                TextColumn::make('full_name'),
                TextColumn::make('pipeline_stage_id')
                    ->formatStateUsing(fn ($state) => PipelineStage::find($state)?->name)
                    ->label('Stage'),
            ]);
    }
}
