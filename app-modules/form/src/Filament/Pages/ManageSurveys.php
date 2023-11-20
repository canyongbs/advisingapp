<?php

namespace Assist\Form\Filament\Pages;

use Filament\Pages\Page;

class ManageSurveys extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';

    protected static string $view = 'assist.filament.pages.coming-soon';

    protected static ?string $navigationGroup = 'Forms and Surveys';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Manage Surveys';
}
