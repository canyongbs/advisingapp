<?php

namespace AdvisingApp\Segment\Actions;

use Filament\Forms\Form;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use AdvisingApp\Segment\Models\Segment;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use AdvisingApp\Segment\Enums\SegmentType;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Jobs\BulkSegmentActionJob;

class BulkSegmentAction
{
    public static function make(string $context)
    {
        return BulkAction::make('segment')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->label('Create Segment')
            ->form(fn (Form $form) => $form->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500),
            ]))
            ->action(function (Collection $records, array $data) use ($context) {
                try {
                    $data['type'] = SegmentType::Static;
                    $data['filters'] = [];
                    $data['model'] = $context == 'students' ? SegmentModel::Student : SegmentModel::Prospect;
                    $segment = Segment::create($data);
                    $user = auth()->user();
                    BulkSegmentActionJob::dispatch($records, $data, $context, $user, $segment);
                    DB::commit();
                    Notification::make()
                        ->title('Segment created')
                        ->body('The segment has been successfully created and is being processed.')
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Notification::make()
                    ->title('Could not save segment')
                    ->body('We failed to create the segment. Please try again later.')
                    ->danger()
                    ->send();
                }
            });
    }
}
