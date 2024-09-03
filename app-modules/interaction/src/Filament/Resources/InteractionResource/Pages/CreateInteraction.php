<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Interaction\Filament\Resources\InteractionResource\Pages;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use Filament\Resources\RelationManagers\RelationManager;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Filament\Resources\InteractionResource;
use AdvisingApp\Interaction\Filament\Actions\DraftInteractionWithAiAction;

class CreateInteraction extends CreateRecord
{
    protected static string $resource = InteractionResource::class;

    public function form(Form $form): Form
    {
        $calculateEndDateTime = function (Get $get, Set $set) {
            $startDateTime = $get('start_datetime');

            if (blank($startDateTime)) {
                $set('end_datetime', null);

                return;
            }

            $duration = $get('duration');

            if (blank($duration)) {
                $set('end_datetime', null);

                return;
            }

            $set('end_datetime', Carbon::parse($startDateTime)
                ->addMinutes($duration)
                ->toDateTimeString());
        };

        return $form
            ->schema([
                MorphToSelect::make('interactable')
                    ->label('Related To')
                    ->searchable()
                    ->required()
                    ->types([
                        ...(auth()->user()->hasLicense(Student::getLicenseType()) ? [MorphToSelect\Type::make(Student::class)
                            ->titleAttribute(Student::displayNameKey())] : []),
                        ...(auth()->user()->hasLicense(Prospect::getLicenseType()) ? [MorphToSelect\Type::make(Prospect::class)
                            ->titleAttribute(Prospect::displayNameKey())] : []),
                        MorphToSelect\Type::make(ServiceRequest::class)
                            ->label('Service Request')
                            ->titleAttribute('service_request_number'),
                    ])
                    ->hiddenOn([RelationManager::class, ManageRelatedRecords::class]),
                Fieldset::make('Details')
                    ->schema([
                        Select::make('interaction_initiative_id')
                            ->relationship('initiative', 'name')
                            ->preload()
                            ->label('Initiative')
                            ->required()
                            ->default(
                                fn () => InteractionInitiative::query()
                                    ->where('is_default', true)
                                    ->first()
                                    ?->getKey()
                            )
                            ->exists((new InteractionInitiative())->getTable(), 'id'),
                        Select::make('interaction_driver_id')
                            ->relationship('driver', 'name')
                            ->preload()
                            ->label('Driver')
                            ->default(
                                fn () => InteractionDriver::query()
                                    ->where('is_default', true)
                                    ->first()
                                    ?->getKey()
                            )
                            ->required()
                            ->exists((new InteractionDriver())->getTable(), 'id'),
                        Select::make('division_id')
                            ->relationship('division', 'name')
                            ->default(fn () => auth()->user()->teams()->first()?->division?->getKey())
                            ->preload()
                            ->label('Division')
                            ->required()
                            ->exists((new Division())->getTable(), 'id'),
                        Select::make('interaction_outcome_id')
                            ->relationship('outcome', 'name')
                            ->default(fn () => InteractionOutcome::query()
                                ->where('is_default', true)
                                ->first()
                                ?->getKey())
                            ->preload()
                            ->label('Outcome')
                            ->required()
                            ->exists((new InteractionOutcome())->getTable(), 'id'),
                        Select::make('interaction_relation_id')
                            ->relationship('relation', 'name')
                            ->default(fn () => InteractionRelation::query()
                                ->where('is_default', true)
                                ->first()
                                ?->getKey())
                            ->preload()
                            ->label('Relation')
                            ->required()
                            ->exists((new InteractionRelation())->getTable(), 'id'),
                        Select::make('interaction_status_id')
                            ->relationship('status', 'name')
                            ->default(fn () => InteractionStatus::query()
                                ->where('is_default', true)
                                ->first()
                                ?->getKey())
                            ->preload()
                            ->label('Status')
                            ->required()
                            ->exists((new InteractionStatus())->getTable(), 'id'),
                        Select::make('interaction_type_id')
                            ->relationship('type', 'name')
                            ->preload()
                            ->default(
                                fn () => InteractionType::query()
                                    ->where('is_default', true)
                                    ->first()
                                    ?->getKey()
                            )
                            ->label('Type')
                            ->required()
                            ->exists((new InteractionType())->getTable(), 'id'),
                    ]),
                Fieldset::make('Time')
                    ->schema([
                        DateTimePicker::make('start_datetime')
                            ->label('Start Date and Time')
                            ->seconds(false)
                            ->default(fn () => now()->toDateTimeString())
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated($calculateEndDateTime),
                        TextInput::make('duration')
                            ->label('Duration (Minutes)')
                            ->integer()
                            ->minValue(0)
                            ->required()
                            ->dehydrated(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated($calculateEndDateTime),
                        DateTimePicker::make('end_datetime')
                            ->label('End Date and Time')
                            ->seconds(false)
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ]),
                Fieldset::make('Notes')
                    ->schema([
                        TextInput::make('subject')
                            ->required(),
                        Textarea::make('description')
                            ->required(),
                    ])
                    ->columns(1),
                Actions::make([
                    DraftInteractionWithAiAction::make(),
                ])
                    ->visible(
                        auth()->user()->hasLicense(LicenseType::ConversationalAi)
                    ),
            ]);
    }
}
