<?php

namespace AdvisingApp\CaseManagement\Filament\Actions;

use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkCreateCaseAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('createCase')
            ->label('Create Case')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->modalHeading('Create Case')
            ->form([
                Select::make('division_id')
                    ->relationship('division', 'name')
                    ->model(CaseModel::class)
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
                Select::make('status_id')
                    ->relationship('status', 'name')
                    ->model(CaseModel::class)
                    ->preload()
                    ->label('Status')
                    ->required()
                    ->exists((new CaseStatus())->getTable(), 'id'),
                Select::make('priority_id')
                    ->relationship(
                        name: 'priority',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('order'),
                    )
                    ->model(CaseModel::class)
                    ->label('Priority')
                    ->required()
                    ->exists((new CasePriority())->getTable(), 'id'),
                /*Select::make('assigned_to_id')
                    ->relationship('assignedTo.user', 'name')
                    ->model(CaseModel::class)
                    ->searchable()
                    ->label('Assign Case to')
                    ->nullable()
                    ->exists((new User())->getTable(), 'id'),*/
                Textarea::make('close_details')
                    ->label('Close Details/Description')
                    ->nullable()
                    ->string(),
                Textarea::make('res_details')
                    ->label('Internal Case Details')
                    ->nullable()
                    ->string(),
            ])
            ->action(function (Collection $records, array $data) {
                try {
                    DB::beginTransaction();

                    $records->chunk(100)->each(function ($chunk) use ($data) {
                        $chunk->each(function ($record) use ($data) {
                            throw_unless($record instanceof Student || $record instanceof Prospect, new Exception('Record must be of type student or prospect.'));
                            $record->cases()->create([
                                ...$data,
                            ]);
                        });
                    });

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::info($e->getMessage());
                    Notification::make()
                        ->title('Could not save case')
                        ->body('We failed to create the case. Please try again later.')
                        ->danger()
                        ->send();

                    return;
                }
                Notification::make()
                    ->title('Case created')
                    ->body('The cases have been created with your selections.')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
