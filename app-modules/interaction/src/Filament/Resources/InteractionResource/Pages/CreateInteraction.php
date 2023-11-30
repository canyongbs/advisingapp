<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Interaction\Filament\Resources\InteractionResource\Pages;

use App\Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Assist\Division\Models\Division;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Assist\Interaction\Models\InteractionType;
use Assist\Interaction\Models\InteractionDriver;
use Assist\Interaction\Models\InteractionStatus;
use Assist\Interaction\Models\InteractionOutcome;
use Assist\Interaction\Models\InteractionCampaign;
use Assist\Interaction\Models\InteractionRelation;
use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\Interaction\Filament\Resources\InteractionResource;
use Filament\Resources\Pages\ManageRelatedRecords;

class CreateInteraction extends CreateRecord
{
    protected static string $resource = InteractionResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                MorphToSelect::make('interactable')
                    ->label('Related To')
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
                    ])
                    ->hiddenOn([RelationManager::class, ManageRelatedRecords::class]),
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
                        Select::make('division_id')
                            ->relationship('division', 'name')
                            ->preload()
                            ->label('Division')
                            ->required()
                            ->exists((new Division())->getTable(), 'id'),
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
}
