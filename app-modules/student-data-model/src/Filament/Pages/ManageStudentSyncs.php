<?php

namespace AdvisingApp\StudentDataModel\Filament\Pages;

use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\Features\StudentDataImportTrackingFeature;
use Filament\Pages\Page;

class ManageStudentSyncs extends Page
{
    protected static ?string $navigationLabel = 'Sync History';

    protected static ?string $title = 'Records Sync';

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationGroup = 'Retention CRM';

    protected static string $view = 'student-data-model::filament.pages.manage-student-syncs';

    public static function canAccess(): bool
    {
        if (! StudentDataImportTrackingFeature::active()) {
            return false;
        }

        if (! app(ManageStudentConfigurationSettings::class)->is_enabled) {
            return false;
        }

        if (! auth()->user()->can('record_sync.view-any')) {
            return false;
        }

        return parent::canAccess();
    }
}
