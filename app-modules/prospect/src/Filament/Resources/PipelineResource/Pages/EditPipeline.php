<?php

namespace AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Prospect\Models\Pipeline;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource;

class EditPipeline extends EditRecord
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
                    ->relationship('segment', 'name')
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
            $resource::getUrl('edit', ['record' => $record]) => Str::limit($record->name, 16),
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
}
