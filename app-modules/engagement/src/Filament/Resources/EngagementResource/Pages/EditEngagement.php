<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\AssistDataModel\Models\Student;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Assist\Engagement\Filament\Resources\EngagementResource;

class EditEngagement extends EditRecord
{
    protected static string $resource = EngagementResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                TextInput::make('subject')
                    ->autofocus()
                    ->required()
                    ->placeholder(__('Subject')),
                Textarea::make('description')
                    ->autofocus()
                    ->placeholder(__('Description'))
                    ->columnSpanFull(),
                MorphToSelect::make('recipient')
                    ->label('Recipient')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->types([
                        MorphToSelect\Type::make(Student::class)
                            ->titleAttribute('full'),
                        MorphToSelect\Type::make(Prospect::class)
                            ->titleAttribute('full'),
                    ]),
                Fieldset::make('Send your engagement')
                    ->schema([
                        Toggle::make('send_later')
                            ->reactive()
                            ->helperText('By default, this engagement will send as soon as it is created unless you schedule it to send later.'),
                        DateTimePicker::make('deliver_at')
                            ->required()
                            ->visible(fn (callable $get) => $get('send_later')),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
