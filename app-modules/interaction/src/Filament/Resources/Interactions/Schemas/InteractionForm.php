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

namespace AdvisingApp\Interaction\Filament\Resources\Interactions\Schemas;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Interaction\Filament\Actions\DraftInteractionWithAiAction;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Interaction\Settings\InteractionManagementSettings;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\Scopes\ExcludeConvertedProspects;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class InteractionForm
{
    public static function configure(Schema $form): Schema
    {
        return $form;
    }

    /**
     * @return array<Step>
     */
    public static function getSteps(): array
    {
        $settings = resolve(InteractionManagementSettings::class);

        return [
            Step::make('Confidentiality')
                ->schema([
                    MorphToSelect::make('interactable')
                        ->label('Related To')
                        ->searchable()
                        ->required()
                        ->types([
                            ...(auth()->user()->hasLicense(Student::getLicenseType()) ? [Type::make(Student::class)
                                ->titleAttribute(Student::displayNameKey())] : []),
                            ...(auth()->user()->hasLicense(Prospect::getLicenseType()) ? [Type::make(Prospect::class)
                                ->titleAttribute(Prospect::displayNameKey())
                                ->modifyOptionsQueryUsing(fn (Builder $query) => $query->tap(new ExcludeConvertedProspects())),
                            ] : []),
                            Type::make(CaseModel::class)
                                ->label('Case')
                                ->titleAttribute('case_number'),
                        ])
                        ->hiddenOn([RelationManager::class, ManageRelatedRecords::class]),
                    Fieldset::make('Confidentiality')
                        ->schema([
                            Checkbox::make('is_confidential')
                                ->label('Confidential')
                                ->live()
                                ->columnSpanFull(),
                            Select::make('interaction_confidential_users')
                                ->relationship('confidentialAccessUsers', 'name')
                                ->preload()
                                ->label('Users')
                                ->multiple()
                                ->exists('users', 'id')
                                ->visible(fn (Get $get) => $get('is_confidential')),
                            Select::make('interaction_confidential_teams')
                                ->relationship('confidentialAccessTeams', 'name')
                                ->preload()
                                ->label('Teams')
                                ->multiple()
                                ->exists('teams', 'id')
                                ->visible(fn (Get $get) => $get('is_confidential')),
                        ]),
                ]),
            Step::make('Details')
                ->schema([
                    Select::make('interaction_initiative_id')
                        ->relationship('initiative', 'name')
                        ->preload()
                        ->label('Initiative')
                        ->required(fn () => $settings->is_initiative_required)
                        ->visible(fn () => $settings->is_initiative_enabled)
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
                        ->required(fn () => $settings->is_driver_required)
                        ->visible(fn () => $settings->is_driver_enabled)
                        ->default(
                            fn () => InteractionDriver::query()
                                ->where('is_default', true)
                                ->first()
                                ?->getKey()
                        )
                        ->exists((new InteractionDriver())->getTable(), 'id'),
                    Select::make('division_id')
                        ->relationship('division', 'name')
                        ->default(fn () => auth()->user()->team?->division?->getKey())
                        ->preload()
                        ->label('Division')
                        ->required()
                        ->exists((new Division())->getTable(), 'id')
                        ->visible(fn (): bool => Division::count() > 1)
                        ->dehydratedWhenHidden(),
                    Select::make('interaction_outcome_id')
                        ->relationship('outcome', 'name')
                        ->default(fn () => InteractionOutcome::query()
                            ->where('is_default', true)
                            ->first()
                            ?->getKey())
                        ->preload()
                        ->label('Outcome')
                        ->required(fn () => $settings->is_outcome_required)
                        ->visible(fn () => $settings->is_outcome_enabled)
                        ->exists((new InteractionOutcome())->getTable(), 'id'),
                    Select::make('interaction_relation_id')
                        ->relationship('relation', 'name')
                        ->default(fn () => InteractionRelation::query()
                            ->where('is_default', true)
                            ->first()
                            ?->getKey())
                        ->preload()
                        ->label('Relation')
                        ->required(fn () => $settings->is_relation_required)
                        ->visible(fn () => $settings->is_relation_enabled)
                        ->exists((new InteractionRelation())->getTable(), 'id'),
                    Select::make('interaction_status_id')
                        ->relationship('status', 'name')
                        ->default(fn () => InteractionStatus::query()
                            ->where('is_default', true)
                            ->first()
                            ?->getKey())
                        ->preload()
                        ->label('Status')
                        ->required(fn () => $settings->is_status_required)
                        ->visible(fn () => $settings->is_status_enabled)
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
                        ->required(fn () => $settings->is_type_required)
                        ->visible(fn () => $settings->is_type_enabled)
                        ->exists((new InteractionType())->getTable(), 'id'),
                ])
                ->columns(2),
            Step::make('Time')
                ->schema([
                    DateTimePicker::make('start_datetime')
                        ->label('Start Date and Time')
                        ->seconds(false)
                        ->default(fn () => now()->toDateTimeString())
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(static::calculateEndDateTime(...)),
                    TextInput::make('duration')
                        ->label('Duration (Minutes)')
                        ->integer()
                        ->minValue(0)
                        ->required()
                        ->dehydrated(false)
                        ->live(onBlur: true)
                        ->afterStateUpdated(static::calculateEndDateTime(...)),
                    DateTimePicker::make('end_datetime')
                        ->label('End Date and Time')
                        ->seconds(false)
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                ]),
            Step::make('Notes')
                ->schema([
                    TextInput::make('subject')
                        ->required(),
                    Textarea::make('description')
                        ->required(),
                    Actions::make([DraftInteractionWithAiAction::make()])
                        ->visible(auth()->user()->hasLicense(LicenseType::ConversationalAi)),
                ])
                ->columns(1),
        ];
    }

    private static function calculateEndDateTime(Get $get, Set $set): void
    {
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
            ->addMinutes((int) $duration)
            ->toDateTimeString());
    }
}
