<?php

namespace Assist\Form\Filament\Resources;

use Assist\Form\Models\Form;
use Filament\Resources\Resource;
use Assist\Form\Filament\Resources\FormResource\Pages\EditForm;
use Assist\Form\Filament\Resources\FormResource\Pages\ListForms;
use Assist\Form\Filament\Resources\FormResource\Pages\CreateForm;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'edit' => EditForm::route('/{record}/edit'),
        ];
    }
}
