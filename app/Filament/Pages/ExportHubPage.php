<?php

namespace App\Filament\Pages;

use App\Features\ExportHubFeature;
use App\Models\User;
use Filament\Pages\Page;
use UnitEnum;

class ExportHubPage extends Page
{
    protected string $view = 'filament.pages.export-hub-page';

    protected static string | UnitEnum | null $navigationGroup = 'Data and Analytics';

    protected static ?string $navigationLabel = 'Export Hub';

    protected static ?string $title = 'Export Hub';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        assert($user instanceof User);

        return ExportHubFeature::active() && $user->can('export_hub.view-any');
    }
}
