<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\KeyValue;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Builder\Block;
use Assist\Form\Filament\Resources\FormResource;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->autocomplete(false),
                Builder::make('content')
                    ->columnSpanFull()
                    ->blocks([
                        Block::make('text_input')
                            ->label('Text Input')
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->string()
                                    ->maxLength(255),
                                // TextInput::make('key')
                                //     ->required()
                                //     ->string()
                                //     ->maxLength(255),
                            ]),
                        Block::make('text_area')
                            ->label('Text Area')
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->string()
                                    ->maxLength(255),
                                // TextInput::make('key')
                                //     ->required()
                                //     ->string()
                                //     ->maxLength(255),
                            ]),
                        Block::make('select')
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->string()
                                    ->maxLength(255),
                                // TextInput::make('key')
                                //     ->required()
                                //     ->string()
                                //     ->maxLength(255),
                                KeyValue::make('options'),
                            ]),
                    ]),
            ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);

        ray($record, $data);

        foreach ($data['content'] as $item) {
            ray($item);
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
