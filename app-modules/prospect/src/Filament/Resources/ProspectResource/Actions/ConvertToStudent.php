<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Actions;

use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\StudentDataModel\Models\Student;

class ConvertToStudent extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalHeading('Convert Prospect to Student')
            ->modalWidth(MaxWidth::ExtraLarge)
            ->modalSubmitActionLabel('Convert')
            ->form([
                Select::make('student_id')
                    ->relationship('student', 'full_name')
                    ->native(false)
                    ->required()
                    ->label('Select Student')
                    ->searchable(),
            ])
            ->action(function ($data, $record) {
               
                /** @var Student $student */
                $student = Student::find($data['student_id']);
                
                if (! $student) {
                    Notification::make()
                        ->title('Student not found!')
                        ->danger()
                        ->send();

                    $this->halt();

                    return;
                }

                $record->student()->associate($student);

                $record->save();
                
                Notification::make()
                    ->title('Prospect converted to Student')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'convert';
    }
}
