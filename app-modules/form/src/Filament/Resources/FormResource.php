<?php

namespace Assist\Form\Filament\Resources;

use Assist\Form\Models\Form;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Assist\Form\Filament\Resources\FormResource\Pages\EditForm;
use Assist\Form\Filament\Resources\FormResource\Pages\ListForms;
use Assist\Form\Filament\Resources\FormResource\Pages\CreateForm;
use Assist\Form\Filament\Resources\FormResource\Pages\ManageFormSubmissions;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['items']);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditForm::class,
            ManageFormSubmissions::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'edit' => EditForm::route('/{record}/edit'),
            'manage-submissions' => ManageFormSubmissions::route('/{record}/submissions'),
        ];
    }
}
