<?php

namespace AdvisingApp\Report\Abstract;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Abstract\Concerns\HasFiltersForm;
use App\Models\User;
use Filament\Pages\Dashboard;

abstract class RecruitmentCrmDashboardReport extends Dashboard
{
    use HasFiltersForm;

    protected static string $view = 'report::filament.pages.report';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->hasLicense(LicenseType::RecruitmentCrm) && $user->can('report-library.view-any');
    }
}
