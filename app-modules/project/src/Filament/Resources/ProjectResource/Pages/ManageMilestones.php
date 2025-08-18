<?php

namespace AdvisingApp\Project\Filament\Resources\ProjectResource\Pages;

use AdvisingApp\Project\Filament\Resources\ProjectResource;
use AdvisingApp\Project\Models\ProjectMilestone;
use App\Features\ProjectMilestoneFeature;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageMilestones extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'milestones';

    public static function getNavigationLabel(): string
    {
        return 'Milestones';
    }

    public static function canAccess(array $arguments = []): bool
    {
        $user = auth()->user();

        return ProjectMilestoneFeature::active() && $user->can('viewAny', [ProjectMilestone::class, $arguments['record']]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->maxLength(65535),
                Select::make('status_id')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->relationship('status', 'name'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                IdColumn::make(),
                TextColumn::make('title'),
                TextColumn::make('description'),
                TextColumn::make('status.name')
                    ->label('Status'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime(),
                TextColumn::make('createdBy.name')
                    ->default('N/A')
                    ->label('Created By'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize('create', $this->getOwnerRecord()),
            ])
            ->actions([
                EditAction::make()
                    ->authorize('update', $this->getOwnerRecord()),
                DeleteAction::make()
                    ->authorize('update', $this->getOwnerRecord()),
            ]);
    }
}
