<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Assist\Form\Models\Form;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\Form\Actions\GenerateFormEmbedCode;
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
            Action::make('view')
                ->url(fn (Form $form) => route('forms.show', ['form' => $form]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            Action::make('embed_snippet')
                ->label('Embed Snippet')
                ->infolist(
                    [
                        TextEntry::make('snippet')
                            ->label('Click to Copy')
                            ->state(function (Form $form) {
                                $code = resolve(GenerateFormEmbedCode::class)->handle($form);

                                return <<<EOD
                                ```
                                {$code}
                                ```
                                EOD;
                            })
                            ->markdown()
                            ->copyable()
                            ->copyableState(fn (Form $form) => resolve(GenerateFormEmbedCode::class)->handle($form))
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500),
                    ]
                )
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->hidden(fn (Form $form) => ! $form->embed_enabled),
            DeleteAction::make(),
        ];
    }
}
