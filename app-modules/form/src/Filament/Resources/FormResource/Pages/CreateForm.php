<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Assist\Form\Filament\Resources\FormResource;
use Assist\Form\Filament\Resources\FormResource\Pages\Concerns\HasSharedFormConfiguration;

class CreateForm extends CreateRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = FormResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->fields());
    }
}
