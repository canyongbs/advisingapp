<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Assist\Form\Models\Form;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Pages\EditRecord;
use Assist\Form\Filament\Resources\FormResource;
use Assist\Form\Filament\Blocks\SelectFormFieldBlock;
use Assist\Form\Filament\Blocks\TextAreaFormFieldBlock;
use Assist\Form\Filament\Blocks\TextInputFormFieldBlock;

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
                    ->autocomplete(false)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->string()
                    ->columnSpanFull(),
                Section::make('Fields')
                    ->schema([
                        Builder::make('fields')
                            ->label('')
                            ->columnSpanFull()
                            ->reorderableWithDragAndDrop(false)
                            ->reorderableWithButtons()
                            ->blocks([
                                TextInputFormFieldBlock::make(),
                                TextAreaFormFieldBlock::make(),
                                SelectFormFieldBlock::make(),
                            ]),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        //TODO: Look into versioning of form/form field configuration

        /** @var Form $record */
        $record = $this->getRecord();

        $data['fields'] = $record
            ->fields
            ->map(fn ($field) => [
                'type' => $field['type'],
                'data' => [
                    'label' => $field['label'],
                    'key' => $field['key'],
                    'required' => $field['required'],
                    ...$field['config'],
                ],
            ])
            ->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        //TODO: Look into versioning of form/form field configuration

        $record = parent::handleRecordUpdate($record, $data);

        $record->fields()->delete();

        collect($data['fields'])
            ->each(function ($field) use ($record) {
                $data = collect($field['data']);

                $record
                    ->fields()
                    ->create([
                        'key' => $data->get('key'),
                        'type' => $field['type'],
                        'label' => $data->get('label'),
                        'required' => $data->get('required'),
                        'config' => $data->except(['key', 'label', 'required']),
                    ]);
            });

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Embed')
                ->url(fn (Form $form) => route('forms.show', ['form' => $form]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
