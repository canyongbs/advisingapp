<?php

namespace Assist\Engagement\Filament\Resources\EngagementFileResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Assist\Engagement\Filament\Resources\EngagementFileResource;

class EditEngagementFile extends EditRecord
{
    protected static string $resource = EngagementFileResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->label('Description')
                    ->nullable()
                    ->string(),
                SpatieMediaLibraryFileUpload::make('file')
                    ->label('Replace File')
                    // TODO: Determine if this is needed
                    //->visibility('private')
                    ->disk('s3')
                    ->collection('file'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
