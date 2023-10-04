<?php

namespace Assist\Interaction\Filament\Resources\InteractionResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\AssistDataModel\Models\Student;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Assist\Interaction\Models\InteractionType;
use Assist\Interaction\Models\InteractionDriver;
use Assist\Interaction\Models\InteractionStatus;
use Assist\Interaction\Models\InteractionOutcome;
use Assist\Interaction\Models\InteractionCampaign;
use Assist\Interaction\Models\InteractionRelation;
use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\Interaction\Models\InteractionInstitution;
use Assist\Interaction\Filament\Resources\InteractionResource;

class EditInteraction extends EditRecord
{
    protected static string $resource = InteractionResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                MorphToSelect::make('interactable')
                    ->label('Interacted With')
                    ->translateLabel()
                    ->searchable()
                    ->required()
                    ->types([
                        MorphToSelect\Type::make(Student::class)
                            ->titleAttribute(Student::displayNameKey()),
                        MorphToSelect\Type::make(Prospect::class)
                            ->titleAttribute(Prospect::displayNameKey()),
                        MorphToSelect\Type::make(ServiceRequest::class)
                            ->label('Service Request')
                            ->titleAttribute('service_request_number'),
                    ]),
                Fieldset::make('Details')
                    ->schema([
                        Select::make('interaction_campaign_id')
                            ->relationship('campaign', 'name')
                            ->preload()
                            ->label('Campaign')
                            ->required()
                            ->exists((new InteractionCampaign())->getTable(), 'id'),
                        Select::make('interaction_driver_id')
                            ->relationship('driver', 'name')
                            ->preload()
                            ->label('Driver')
                            ->required()
                            ->exists((new InteractionDriver())->getTable(), 'id'),
                        Select::make('interaction_institution_id')
                            ->relationship('institution', 'name')
                            ->preload()
                            ->label('Institution')
                            ->required()
                            ->exists((new InteractionInstitution())->getTable(), 'id'),
                        Select::make('interaction_outcome_id')
                            ->relationship('outcome', 'name')
                            ->preload()
                            ->label('Outcome')
                            ->required()
                            ->exists((new InteractionOutcome())->getTable(), 'id'),
                        Select::make('interaction_relation_id')
                            ->relationship('relation', 'name')
                            ->preload()
                            ->label('Relation')
                            ->required()
                            ->exists((new InteractionRelation())->getTable(), 'id'),
                        Select::make('interaction_status_id')
                            ->relationship('status', 'name')
                            ->preload()
                            ->label('Status')
                            ->required()
                            ->exists((new InteractionStatus())->getTable(), 'id'),
                        Select::make('interaction_type_id')
                            ->relationship('type', 'name')
                            ->preload()
                            ->label('Type')
                            ->required()
                            ->exists((new InteractionType())->getTable(), 'id'),
                    ]),
                Fieldset::make('Time')
                    ->schema([
                        DateTimePicker::make('start_datetime')
                            ->required(),
                        DateTimePicker::make('end_datetime')
                            ->required(),
                    ]),
                Fieldset::make('Notes')
                    ->schema([
                        TextInput::make('subject')
                            ->required(),
                        Textarea::make('description')
                            ->required(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
