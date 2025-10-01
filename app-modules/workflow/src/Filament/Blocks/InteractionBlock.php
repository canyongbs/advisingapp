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

namespace AdvisingApp\Workflow\Filament\Blocks;

use AdvisingApp\Division\Models\Division;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Interaction\Settings\InteractionManagementSettings;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class InteractionBlock extends WorkflowActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Interaction');

        $this->schema($this->createFields());
    }

    //TODO: implement
    public function generateFields(string $fieldPrefix = ''): array
    {
        $settings = app(InteractionManagementSettings::class);

        return [
            Section::make('info')
                ->schema([
                    Select::make($fieldPrefix . 'interaction_initiative_id')
                        ->relationship('initiative', 'name')
                        ->model(Interaction::class)
                        ->label('Initiative')
                        ->required(fn () => $settings->is_initiative_required)
                        ->visible(fn () => $settings->is_initiative_enabled)
                        ->exists((new InteractionInitiative())->getTable(), 'id'),
                    Select::make($fieldPrefix . 'interaction_driver_id')
                        ->relationship('driver', 'name')
                        ->model(Interaction::class)
                        ->preload()
                        ->label('Driver')
                        ->required(fn () => $settings->is_driver_required)
                        ->visible(fn () => $settings->is_driver_enabled)
                        ->exists((new InteractionDriver())->getTable(), 'id'),
                    Select::make($fieldPrefix . 'division_id')
                        ->relationship('division', 'name')
                        ->model(Interaction::class)
                        ->preload()
                        ->default(
                            fn () => auth()->user()->team?->division?->getKey()
                            ?? Division::query()
                                ->where('is_default', true)
                                ->first()
                                ?->getKey()
                        )
                        ->label('Division')
                        ->visible(function () {
                            return Division::query()->where('is_default', false)->exists();
                        })
                        ->dehydratedWhenHidden()
                        ->required()
                        ->exists((new Division())->getTable(), 'id'),
                    Select::make($fieldPrefix . 'interaction_outcome_id')
                        ->relationship('outcome', 'name')
                        ->model(Interaction::class)
                        ->preload()
                        ->label('Outcome')
                        ->required(fn () => $settings->is_outcome_required)
                        ->visible(fn () => $settings->is_outcome_enabled)
                        ->exists((new InteractionOutcome())->getTable(), 'id'),
                    Select::make($fieldPrefix . 'interaction_relation_id')
                        ->relationship('relation', 'name')
                        ->model(Interaction::class)
                        ->preload()
                        ->label('Relation')
                        ->required(fn () => $settings->is_relation_required)
                        ->visible(fn () => $settings->is_relation_enabled)
                        ->exists((new InteractionRelation())->getTable(), 'id'),
                    Select::make($fieldPrefix . 'interaction_status_id')
                        ->relationship('status', 'name')
                        ->model(Interaction::class)
                        ->preload()
                        ->label('Status')
                        ->required(fn () => $settings->is_status_required)
                        ->visible(fn () => $settings->is_status_enabled)
                        ->exists((new InteractionStatus())->getTable(), 'id'),
                    Select::make($fieldPrefix . 'interaction_type_id')
                        ->relationship('type', 'name')
                        ->model(Interaction::class)
                        ->preload()
                        ->label('Type')
                        ->required(fn () => $settings->is_type_required)
                        ->visible(fn () => $settings->is_type_enabled)
                        ->exists((new InteractionType())->getTable(), 'id'),
                ])
                ->columns(2),
            Section::make('Time')
                ->schema([
                DateTimePicker::make($fieldPrefix . 'start_datetime')
                        ->seconds(false)
                        ->required(),
                DateTimePicker::make($fieldPrefix . 'end_datetime')
                        ->seconds(false)
                        ->required(),
            ])->columns(2),
            Section::make('Notes')
                ->schema([
                TextInput::make($fieldPrefix . 'subject')
                        ->required(),
                Textarea::make($fieldPrefix . 'description')
                        ->required(),
            ])->columns(2),
            Section::make('How long after the previous step should this occur?')
                ->schema([
                TextInput::make('days')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                TextInput::make('hours')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                TextInput::make('minutes')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
            ])
                ->columns(3),
        ];

        // return [
        //   Select::make($fieldPrefix . 'interaction_initiative_id')
        //     ->relationship('initiative', 'name')
        //     ->model(Interaction::class)
        //     ->label('Initiative')
        //     ->required(fn() => $settings->is_initiative_required)
        //     ->visible(fn() => $settings->is_initiative_enabled)
        //     ->exists((new InteractionInitiative())->getTable(), 'id'),
        //   Select::make($fieldPrefix . 'interaction_driver_id')
        //     ->relationship('driver', 'name')
        //     ->model(Interaction::class)
        //     ->preload()
        //     ->label('Driver')
        //     ->required(fn() => $settings->is_driver_required)
        //     ->visible(fn() => $settings->is_driver_enabled)
        //     ->exists((new InteractionDriver())->getTable(), 'id'),
        //   Select::make($fieldPrefix . 'division_id')
        //     ->relationship('division', 'name')
        //     ->model(Interaction::class)
        //     ->preload()
        //     ->default(
        //       fn() => auth()->user()->team?->division?->getKey()
        //         ?? Division::query()
        //         ->where('is_default', true)
        //         ->first()
        //         ?->getKey()
        //     )
        //     ->label('Division')
        //     ->visible(function () {
        //       return Division::query()->where('is_default', false)->exists();
        //     })
        //     ->dehydratedWhenHidden()
        //     ->required()
        //     ->exists((new Division())->getTable(), 'id'),
        //   Select::make($fieldPrefix . 'interaction_outcome_id')
        //     ->relationship('outcome', 'name')
        //     ->model(Interaction::class)
        //     ->preload()
        //     ->label('Outcome')
        //     ->required(fn() => $settings->is_outcome_required)
        //     ->visible(fn() => $settings->is_outcome_enabled)
        //     ->exists((new InteractionOutcome())->getTable(), 'id'),
        //   Select::make($fieldPrefix . 'interaction_relation_id')
        //     ->relationship('relation', 'name')
        //     ->model(Interaction::class)
        //     ->preload()
        //     ->label('Relation')
        //     ->required(fn() => $settings->is_relation_required)
        //     ->visible(fn() => $settings->is_relation_enabled)
        //     ->exists((new InteractionRelation())->getTable(), 'id'),
        //   Select::make($fieldPrefix . 'interaction_status_id')
        //     ->relationship('status', 'name')
        //     ->model(Interaction::class)
        //     ->preload()
        //     ->label('Status')
        //     ->required(fn() => $settings->is_status_required)
        //     ->visible(fn() => $settings->is_status_enabled)
        //     ->exists((new InteractionStatus())->getTable(), 'id'),
        //   Select::make($fieldPrefix . 'interaction_type_id')
        //     ->relationship('type', 'name')
        //     ->model(Interaction::class)
        //     ->preload()
        //     ->label('Type')
        //     ->required(fn() => $settings->is_type_required)
        //     ->visible(fn() => $settings->is_type_enabled)
        //     ->exists((new InteractionType())->getTable(), 'id'),
        //   DateTimePicker::make($fieldPrefix . 'start_datetime')
        //     ->seconds(false)
        //     ->required(),
        //   DateTimePicker::make($fieldPrefix . 'end_datetime')
        //     ->seconds(false)
        //     ->required(),
        //   TextInput::make($fieldPrefix . 'subject')
        //     ->required(),
        //   Textarea::make($fieldPrefix . 'description')
        //     ->required(),
        //   Section::make('How long after the previous step should this occur?')
        //     ->schema([
        //       TextInput::make('days')
        //         ->translateLabel()
        //         ->numeric()
        //         ->step(1)
        //         ->minValue(0)
        //         ->default(0)
        //         ->inlineLabel(),
        //       TextInput::make('hours')
        //         ->translateLabel()
        //         ->numeric()
        //         ->step(1)
        //         ->minValue(0)
        //         ->default(0)
        //         ->inlineLabel(),
        //       TextInput::make('minutes')
        //         ->translateLabel()
        //         ->numeric()
        //         ->step(1)
        //         ->minValue(0)
        //         ->default(0)
        //         ->inlineLabel(),
        //     ])
        //     ->columns(3),
        // ];
    }

    public static function type(): string
    {
        return 'workflow_interaction_block';
    }
}
