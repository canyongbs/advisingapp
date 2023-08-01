<?php

namespace Assist\Case\Filament\Resources\CaseItemResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Assist\AssistDataModel\Models\Student;
use Filament\Resources\RelationManagers\RelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource;

class RespondentRelationManager extends RelationManager
{
    protected static string $relationship = 'respondent';

    protected static ?string $recordTitleAttribute = 'full';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->state(function (Student $student) {
                        return str($student->getMorphClass())->ucfirst();
                    }),
                Tables\Columns\TextColumn::make('full')
                    ->label('Name'),
            ])
            ->paginated(false)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn ($record) => match ($record->getMorphClass()) {
                        'student' => StudentResource::getUrl('view', ['record' => $record]),
                    }),
            ]);
    }
}
