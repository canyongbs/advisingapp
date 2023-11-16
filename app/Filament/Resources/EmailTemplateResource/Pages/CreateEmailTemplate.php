<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use Filament\Forms\Form;
use Filament\Support\Colors\Color;
use Assist\Division\Models\Division;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MorphToSelect;
use App\Filament\Resources\EmailTemplateResource;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class CreateEmailTemplate extends CreateRecord
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
                Select::make('primary_color')
                    ->allowHtml()
                    ->native(false)
                    ->options(
                        collect(Color::all())
                            ->keys()
                            ->sort()
                            ->mapWithKeys(fn (string $color) => [
                                $color => "<span class='flex items-center gap-x-4'>
                                <span class='rounded-full w-4 h-4' style='background:rgb(" . Color::all()[$color]['500'] . ")'></span>
                                <span>" . str($color)->headline() . '</span>
                                </span>',
                            ])
                    ),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->disk('s3')
                    ->collection('logo')
                    ->visibility('private')
                    ->image(),
            ]);
    }
}
