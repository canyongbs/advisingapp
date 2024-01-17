<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\DateTimePicker;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource;

class EditChangeRequest extends EditRecord
{
    protected static string $resource = ChangeRequestResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Change Request Details')
                    ->aside()
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('change_request_type_id')
                            ->relationship('type', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                        Select::make('change_request_status_id')
                            ->relationship('status', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),
                        Textarea::make('reason')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('backout_strategy')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
                        DateTimePicker::make('start_time')
                            ->required()
                            ->columnSpan(1),
                        DateTimePicker::make('end_time')
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                Section::make('Risk Management')
                    ->aside()
                    ->schema([
                        TextInput::make('impact')
                            ->reactive()
                            ->helperText('Please enter a number between 1 and 5.')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->columnSpan(1),
                        TextInput::make('likelihood')
                            ->reactive()
                            ->helperText('Please enter a number between 1 and 5.')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(5)
                            ->columnSpan(1),
                        ViewField::make('risk_score')
                            ->view('filament.forms.components.change-request.calculated-risk-score'),
                    ])
                    ->columns(3),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
