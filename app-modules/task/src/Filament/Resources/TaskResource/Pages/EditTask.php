<?php

namespace Assist\Task\Filament\Resources\TaskResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Assist\Task\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use Assist\Task\Filament\Concerns\TaskEditForm;
use Assist\Task\Filament\Resources\TaskResource;

class EditTask extends EditRecord
{
    use TaskEditForm;

    protected static string $resource = TaskResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->editFormFields());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        Task::unguarded(fn () => $record->update($data));

        return $record;
    }
}
