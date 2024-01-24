<?php

namespace AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource\Pages;

use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource;
use AdvisingApp\ServiceManagement\Filament\Resources\ServiceRequestFormResource\Pages\Concerns\HasSharedFormConfiguration;

class CreateServiceRequestForm extends CreateRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = ServiceRequestFormResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->fields());
    }
}
