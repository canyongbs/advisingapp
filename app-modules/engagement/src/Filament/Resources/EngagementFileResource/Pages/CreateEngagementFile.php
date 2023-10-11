<?php

namespace Assist\Engagement\Filament\Resources\EngagementFileResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Assist\Engagement\Filament\Resources\EngagementFileResource;

class CreateEngagementFile extends CreateRecord
{
    protected static string $resource = EngagementFileResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->label('Description')
                    ->nullable()
                    ->string()
                    ->required(),
                SpatieMediaLibraryFileUpload::make('file')
                    ->label('File')
                    // TODO: Determine if this is needed
                    //->visibility('private')
                    ->disk('s3')
                    ->collection('file')
                    ->required(),
            ]);
    }
}
