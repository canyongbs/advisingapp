<?php

namespace AdvisingApp\Alert\Filament\Actions;

use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\Alert\Models\AlertStatus;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BulkCreateAlertAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('createAlert')
            ->label('Create Alert')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->modalHeading('Create Alert')
            ->form([
                Textarea::make('description')
                    ->required()
                    ->string()
                    ->label('Description'),
                Select::make('severity')
                    ->options(AlertSeverity::class)
                    ->default(AlertSeverity::default())
                    ->required()
                    ->enum(AlertSeverity::class)
                    ->label('Severity'),
                Textarea::make('suggested_intervention')
                    ->required()
                    ->string()
                    ->label('Suggested Intervention'),
                Select::make('status_id')
                    ->label('Status')
                    ->options(AlertStatus::orderBy('order')->pluck('name', 'id'))
                    ->default(fn () => SystemAlertStatusClassification::default()?->getKey())
                    ->exists('alert_statuses', 'id')
                    ->required(),
            ])->action(function (Collection $records, array $data) {
                try {
                    DB::beginTransaction();

                    $records->chunk(100)->each(function ($chunk) use ($data) {
                        $chunk->each(function ($record) use ($data) {
                            throw_unless($record instanceof Student || $record instanceof Prospect, new Exception('Record must be of type student or prospect.'));
                            $record->alerts()->create([
                                ...$data,
                            ]);
                        });
                    });

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Notification::make()
                        ->title('Could not save alert')
                        ->body('We failed to create the alert. Please try again later.')
                        ->danger()
                        ->send();

                    return;
                }
                Notification::make()
                    ->title('Alert created')
                    ->body('The alert have been created with your selections.')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
