<?php

namespace Assist\Engagement\Filament\Resources\EngagementFileResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\Engagement\Filament\Resources\EngagementFileResource;

class CreateEngagementFile extends CreateRecord
{
    protected static string $resource = EngagementFileResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema([
                TextInput::make('description')
                    ->label('Description')
                    ->nullable()
                    ->string(),
                // File Upload
            ]);
    }
}
