<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Actions;

use App\Models\Tag;
use App\Enums\TagType;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use AdvisingApp\StudentDataModel\Models\Student;

class StudentTagAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalHeading('Student Tag')
            ->modalWidth(MaxWidth::ExtraLarge)
            ->modalSubmitActionLabel('Save')
            ->form([
                Select::make('tag_id')
                    ->options( fn(): array => Tag::where('type', TagType::Student)->pluck('name', 'id')->toArray())
                    ->required()
                    ->label('Tag')
                    ->multiple()
                    ->required()
                    ->default(fn (?Student $record): array => $record ? $record->tags->pluck('id')->toArray() : [])
                    ->searchable(),
            ])
            ->action(function ($data, Student $record) {
                $record->tags()->sync($data['tag_id']);
                $record->save();

                Notification::make()
                    ->title('Tags added to student.')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'Tags';
    }
}
