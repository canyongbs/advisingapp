<?php

namespace AdvisingApp\Interaction\Filament\Actions;

use AdvisingApp\Division\Models\Division;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Exception;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BulkCreateInteractionAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('createInteraction')
            ->label('Create Interaction')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->modalHeading('Create Interaction')
            ->form([
                Fieldset::make('Details')
                    ->schema([
                        Select::make('interaction_initiative_id')
                            ->relationship('initiative', 'name')
                            ->model(Interaction::class)
                            ->label('Initiative')
                            ->required()
                            ->exists((new InteractionInitiative())->getTable(), 'id'),
                        Select::make('interaction_driver_id')
                            ->relationship('driver', 'name')
                            ->model(Interaction::class)
                            ->preload()
                            ->label('Driver')
                            ->required()
                            ->exists((new InteractionDriver())->getTable(), 'id'),
                        Select::make('division_id')
                            ->relationship('division', 'name')
                            ->model(Interaction::class)
                            ->preload()
                            ->default(
                                fn () => Division::query()
                                    ->where('is_default', true)
                                    ->first()
                                    ?->getKey()
                            )
                            ->label('Division')
                            ->visible(function () {
                                $defaultDivision = Division::query()->where('is_default', true)->count();
                                $totalDivision = Division::query()->count();

                                return $defaultDivision !== $totalDivision;
                            })
                            ->dehydratedWhenHidden()
                            ->required()
                            ->exists((new Division())->getTable(), 'id'),
                        Select::make('interaction_outcome_id')
                            ->relationship('outcome', 'name')
                            ->model(Interaction::class)
                            ->preload()
                            ->label('Outcome')
                            ->required()
                            ->exists((new InteractionOutcome())->getTable(), 'id'),
                        Select::make('interaction_relation_id')
                            ->relationship('relation', 'name')
                            ->model(Interaction::class)
                            ->preload()
                            ->label('Relation')
                            ->required()
                            ->exists((new InteractionRelation())->getTable(), 'id'),
                        Select::make('interaction_status_id')
                            ->relationship('status', 'name')
                            ->model(Interaction::class)
                            ->preload()
                            ->label('Status')
                            ->required()
                            ->exists((new InteractionStatus())->getTable(), 'id'),
                        Select::make('interaction_type_id')
                            ->relationship('type', 'name')
                            ->model(Interaction::class)
                            ->preload()
                            ->label('Type')
                            ->required()
                            ->exists((new InteractionType())->getTable(), 'id'),
                    ]),
                Fieldset::make('Time')
                    ->schema([
                        DateTimePicker::make('start_datetime')
                            ->seconds(false)
                            ->required(),
                        DateTimePicker::make('end_datetime')
                            ->seconds(false)
                            ->required(),
                    ]),
                Fieldset::make('Notes')
                    ->schema([
                        TextInput::make('subject')
                            ->required(),
                        Textarea::make('description')
                            ->required(),
                    ]),
            ])
            ->action(function (Collection $records, array $data) {
                try {
                    DB::beginTransaction();

                    $records->chunk(100)->each(function ($chunk) use ($data) {
                        $chunk->each(function ($record) use ($data) {
                            throw_unless($record instanceof Student || $record instanceof Prospect, new Exception('Record must be of type student or prospect.'));
                            $record->interactions()->create([
                                ...$data,
                            ]);
                        });
                    });

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Notification::make()
                        ->title('Could not save interaction')
                        ->body('We failed to create the interaction. Please try again later.')
                        ->danger()
                        ->send();

                    return;
                }
                Notification::make()
                    ->title('Interaction created')
                    ->body('The interaction has been created with your selections.')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
