<?php

namespace Assist\Application\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Assist\Application\Models\Application;
use Assist\Application\Filament\Resources\ApplicationResource\Pages\EditApplication;
use Assist\Application\Filament\Resources\ApplicationResource\Pages\ListApplications;
use Assist\Application\Filament\Resources\ApplicationResource\Pages\CreateApplication;
use Assist\Application\Filament\Resources\ApplicationResource\Pages\ManageApplicationSubmissions;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Forms and Surveys';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Manage Admissions';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['fields']);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditApplication::class,
            ManageApplicationSubmissions::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplications::route('/'),
            'create' => CreateApplication::route('/create'),
            'edit' => EditApplication::route('/{record}/edit'),
            'manage-submissions' => ManageApplicationSubmissions::route('/{record}/submissions'),
        ];
    }
}
