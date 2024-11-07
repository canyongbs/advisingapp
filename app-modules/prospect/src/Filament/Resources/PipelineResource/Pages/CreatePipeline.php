<?php

namespace AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource;
use AdvisingApp\Prospect\Jobs\PipelineEducatablesMoveIntoStages;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class CreatePipeline extends CreateRecord
{
    protected static string $resource = PipelineResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Name'),
                Select::make('segment_id')
                    ->label('Segment')
                    ->required()
                    ->relationship('segment', 'name',fn(Builder $query) => $query->where('model',app(Prospect::class)->getMorphClass()))
                    ->searchable()
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                    ->preload(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->label('Description'),

                Repeater::make('pipeline_stages')
                    ->relationship('stages')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Stage')
                                    ->distinct()
                                    ->required(),
                                Checkbox::make('is_default')
                                    ->label('Is Default?')
                                    ->inline(false)
                                    ->required(function (Get $get) {
                                        $stages = array_values($get('../'));
                                        $stages = collect($stages);
                                        $hasDefault = collect($stages)->contains('is_default', true);

                                        if (! $hasDefault) {
                                            return true;
                                        }
                                    })
                                    ->fixIndistinctState(),
                            ])
                            ->columns(2),
                    ])
                    ->orderColumn('order')
                    ->reorderable()
                    ->columnSpan('full')
                    ->label('Pipeline Stages')
                    ->minItems(1)
                    ->maxItems(5),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $pipeline = $this->getRecord();
        
        dispatch(new PipelineEducatablesMoveIntoStages(
            pipeline: $this->getRecord()
        ));

        $totalRecords = $pipeline?->segment?->retrieveEducatablesRecords()->count();

        $user = $pipeline->createdBy;

        if($user){
            $user->notify(
                Notification::make()
                        ->title('Pipeline creation started')
                        ->body("Your pipeline creation has begun and {$totalRecords} records will be processed in the background.")
                        ->success()
                        ->toDatabase(),
            );
        }
        
    }
}
