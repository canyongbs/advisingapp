<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Prospect\Filament\Pages;

use AdvisingApp\Prospect\Filament\Widgets\ProspectsActionCenterWidget;
use AdvisingApp\Prospect\Filament\Widgets\ProspectStats;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Abstract\RecruitmentCrmDashboardReport;
use AdvisingApp\StudentDataModel\Enums\ActionCenterTab;
use App\Filament\Clusters\ReportLibrary;
use App\Filament\Widgets\ProspectGrowthChart;
use App\Models\User;
use Livewire\Attributes\Url;
use Symfony\Component\HttpFoundation\Response;

class RecruitmentCrmDashboard extends RecruitmentCrmDashboardReport
{
    protected static ?string $cluster = ReportLibrary::class;

    protected static ?string $navigationGroup = 'Prospects';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Action Center';

    protected static ?string $title = 'Action Center';

    protected static string $routePath = 'recruitment-crm-dashboard';

    protected static ?string $navigationIcon = '';

    #[Url]
    public string $activeTab = ActionCenterTab::Subscribed->value;

    protected static string $view = 'student-data-model::filament.pages.dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->hasLicense(Prospect::getLicenseType());
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        abort_unless($user->hasLicense(Prospect::getLicenseType()), Response::HTTP_FORBIDDEN);

        if (! ActionCenterTab::tryFrom($this->activeTab)) {
            $this->redirect(static::getUrl(['activeTab' => ActionCenterTab::Subscribed->value]), navigate: true);
        }
    }

    public function getWidgets(): array
    {
        return [
            ProspectStats::class,
            ProspectsActionCenterWidget::class,
            ProspectGrowthChart::class,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'activeTab' => $this->activeTab,
        ];
    }
}
