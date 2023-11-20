<?php

namespace Assist\Form\Filament\Pages;

use Filament\Pages\Page;

class ManageAdmissions extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static string $view = 'assist.filament.pages.coming-soon';

    protected static ?string $navigationGroup = 'Forms and Surveys';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Manage Admissions';
}
