<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Assist\Form\Models\Form;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\KeyValue;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Builder\Block;
use Assist\Form\Filament\Resources\FormResource;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected static ?string $navigationLabel = 'Edit';

    public function form(FilamentForm $form): FilamentForm
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
                    ->reorderableWithDragAndDrop(false)
                    ->reorderableWithButtons()
                    ->blocks([
                        Block::make('text_input')
                            ->label('Text Input')
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->string()
                                    ->maxLength(255),
                                TextInput::make('key')
                                    ->required()
                                    ->string()
                                    ->maxLength(255),
                            ]),
                        Block::make('text_area')
                            ->label('Text Area')
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->string()
                                    ->maxLength(255),
                                TextInput::make('key')
                                    ->required()
                                    ->string()
                                    ->maxLength(255),
                            ]),
                        Block::make('select')
                            ->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->string()
                                    ->maxLength(255),
                                TextInput::make('key')
                                    ->required()
                                    ->string()
                                    ->maxLength(255),
                                KeyValue::make('options'),
                            ]),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Form $record */
        $record = $this->getRecord();

        $data['content'] = $record
            ->items
            ->map(fn ($item) => [
                'type' => $item['type'],
                'data' => [
                    'label' => $item['label'],
                    'key' => $item['key'],
                    ...$item['content'],
                ],
            ])
            ->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);

        $record->items()->delete();

        collect($data['content'])
            ->each(function ($item, $index) use ($record) {
                $data = collect($item['data']);

                $record
                    ->items()
                    ->create([
                        'key' => $data->get('key'),
                        'type' => $item['type'],
                        'label' => $data->get('label'),
                        'content' => $data->except(['key', 'label']),
                        'order' => $index,
                    ]);
            });

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Embed')
                ->url(fn (Form $form) => route('forms.embed.show', ['embed' => $form]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
