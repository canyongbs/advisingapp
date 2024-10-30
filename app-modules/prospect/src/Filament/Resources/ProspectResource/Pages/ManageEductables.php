<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\AttachAction;
use AdvisingApp\Prospect\Models\PipelineStage;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource;

class ManageEductables extends ManageRelatedRecords
{
    protected static string $resource = PipelineResource::class;

    public ?string $viewType = 'null';

    protected static string $relationship = 'prospects';

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';

    protected static ?string $title = 'Manage Pipeline Subjects';

    protected static ?string $navigationLabel = 'Pipeline Subjects';

    protected static string $view = 'prospect::filament.pages.manage-pipeline-subjects';

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->viewType = session('pipeline-view-type') ?? 'table';
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
            ])
            ->headerActions([
                AttachAction::make()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('pipeline_stage_id')
                            ->options(PipelineStage::pluck('name', 'id')->toArray()),
                    ]),
            ]);
    }
}
