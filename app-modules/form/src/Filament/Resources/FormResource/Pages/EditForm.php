<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Assist\Form\Models\Form;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Pages\EditRecord;
use Assist\Form\Filament\Resources\FormResource;
use Assist\Form\Filament\Resources\FormResource\Pages\Concerns\HasSharedFormConfiguration;

class EditForm extends EditRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = FormResource::class;

    protected static ?string $navigationLabel = 'Edit';

    public function form(FilamentForm $form): FilamentForm
    {
        return $form
            ->schema($this->fields());
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

        /** @var Form $record */
        $record = parent::handleRecordUpdate($record, $data);

        $record->fields()->delete();

        $this->handleFieldSaving($record, $data['fields']);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Respond')
                ->url(fn (Form $form) => route('forms.show', ['form' => $form]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
