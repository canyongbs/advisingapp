<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Assist\Division\Models\Division;
use App\Forms\Components\ColorSelect;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\MorphToSelect;
use App\Filament\Resources\EmailTemplateResource;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                MorphToSelect::make('relatedTo')
                    ->label('Related To')
                    ->types([
                        Type::make(Division::class)
                            ->titleAttribute('name'),
                    ])
                    ->required(),
                TextInput::make('name')
                    ->string()
                    ->required()
                    ->autocomplete(false),
                ColorSelect::make('primary_color'),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->disk('s3')
                    ->collection('logo')
                    ->visibility('private')
                    ->image(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
