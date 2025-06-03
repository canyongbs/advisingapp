<?php

namespace AdvisingApp\Prospect\Filament\Actions;

use AdvisingApp\Campaign\Settings\CampaignSettings;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\Tag;
use Exception;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;

class ProspectTagsBulkAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('bulkProspectTags')
            ->icon('heroicon-o-tag')
            ->modalHeading('Bulk assign prospect tags')
            ->label('Bulk Prospect Tags')
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} prospects to apply tags.")
            ->form([
                    Select::make('tag_ids')
                        ->label('Which tags should be applied?')
                        ->options(function () {
                            return Tag::where('type', app(Prospect::class)->getMorphClass())->pluck('name', 'id');
                        })
                        ->multiple()
                        ->searchable()
                        ->required()
                        ->exists('tags', 'id'),
                    Toggle::make('remove_prior')
                        ->label('Remove all previously assigned tags?')
                        ->default(false)
                        ->hintIconTooltip('If checked, all prior tags assignments will be removed.'),
                
            ])
            ->action(function (array $data, Collection $records){
                $records->chunk(100)->each(function ($chunk) use ($data) {
                    $chunk->each(function ($record) use ($data) {
                        throw_unless($record instanceof Prospect, new Exception("Record must be of type prospect."));

                        if (!empty($data['tag_ids'])) {
                            $record->tags()
                                    ->sync(
                                        ids: $data['tag_ids'],
                                        detaching: $data['remove_prior']
                                    );
                        }
                    });
                });

                Notification::make()
                    ->title('Tags assigned successfully.')
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
