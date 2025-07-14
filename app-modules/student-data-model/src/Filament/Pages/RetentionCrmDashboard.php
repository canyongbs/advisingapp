<?php

namespace AdvisingApp\StudentDataModel\Filament\Pages;

use AdvisingApp\Report\Abstract\RetentionCrmDashboardReport;
use AdvisingApp\StudentDataModel\Enums\ActionCenterTab;
use AdvisingApp\StudentDataModel\Filament\Widgets\StudentsActionCenterWidget;
use AdvisingApp\StudentDataModel\Filament\Widgets\StudentStats;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use Livewire\Attributes\Url;
use Symfony\Component\HttpFoundation\Response;

class RetentionCrmDashboard extends RetentionCrmDashboardReport
{
    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Students';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Action Center';

    protected static ?string $title = 'Action Center';

    protected static string $routePath = 'retention-crm-dashboard';

    protected static ?string $navigationIcon = '';

    #[Url]
    public string $activeTab = ActionCenterTab::Subscribed->value;

    protected static string $view = 'student-data-model::filament.pages.dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->hasLicense(Student::getLicenseType());
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        abort_unless($user->hasLicense(Student::getLicenseType()), Response::HTTP_FORBIDDEN);

        if (! ActionCenterTab::tryFrom($this->activeTab)) {
            $this->redirect(static::getUrl(['activeTab' => ActionCenterTab::Subscribed->value]), navigate: true);
        }
    }

    public function getWidgets(): array
    {
        return [
            StudentStats::class,
            StudentsActionCenterWidget::class,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'activeTab' => $this->activeTab,
        ];
    }
}
