<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use Assist\Form\Filament\Resources\FormResource;
use Assist\Form\Filament\Resources\FormResource\Pages\Concerns\HasSharedFormConfiguration;

class CreateForm extends CreateRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = FormResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->fields());
    }

    protected function handleRecordCreation(array $data): Model
    {
        /** @var \Assist\Form\Models\Form $record */
        $record = parent::handleRecordCreation($data);

        $this->handleFieldSaving($record, $data['fields']);

        return $record;
    }
}
