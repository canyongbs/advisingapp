<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Assist\Form\Models\Form;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Builder;
use Assist\Form\Filament\Blocks\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Pages\EditRecord;
use Assist\Form\Filament\Blocks\TextArea;
use Assist\Form\Filament\Blocks\TextInput;
use Assist\Form\Filament\Resources\FormResource;
use Filament\Forms\Components\Textarea as FilamentTextarea;
use Filament\Forms\Components\TextInput as FilamentTextInput;

class EditForm extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected static ?string $navigationLabel = 'Edit';

    public function form(FilamentForm $form): FilamentForm
    {
        return parent::form($form)
            ->schema([
                FilamentTextInput::make('name')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->autocomplete(false)
                    ->columnSpanFull(),
                FilamentTextarea::make('description')
                    ->string()
                    ->columnSpanFull(),
                Builder::make('content')
                    ->columnSpanFull()
                    ->reorderableWithDragAndDrop(false)
                    ->reorderableWithButtons()
                    ->blocks([
                        TextInput::make(),
                        TextArea::make(),
                        Select::make(),
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
                    'required' => $item['required'],
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
            ->each(function ($item) use ($record) {
                $data = collect($item['data']);

                $record
                    ->items()
                    ->create([
                        'key' => $data->get('key'),
                        'type' => $item['type'],
                        'label' => $data->get('label'),
                        'required' => $data->get('required'),
                        'content' => $data->except(['key', 'label', 'required']),
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
