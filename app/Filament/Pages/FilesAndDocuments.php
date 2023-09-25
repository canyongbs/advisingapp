<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Pages\Concerns\HasNavigationGroup;

class FilesAndDocuments extends Page
{
    use HasNavigationGroup;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'assist.filament.pages.coming-soon';

    protected static ?string $navigationLabel = 'Files and Documents';

    protected ?string $heading = 'Files and Documents';
}
