<?php

namespace Assist\Application\Filament\Resources\ApplicationResource\Pages;

use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Assist\Application\Filament\Resources\ApplicationResource;
use Assist\Application\Filament\Resources\ApplicationResource\Pages\Concerns\HasSharedFormConfiguration;

class CreateApplication extends CreateRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = ApplicationResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->fields());
    }
}
