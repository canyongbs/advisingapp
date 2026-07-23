<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Report\Enums;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Prospect\Filament\Pages\RecruitmentCrmDashboard;
use AdvisingApp\Report\Filament\Pages\ArtificialIntelligence;
use AdvisingApp\Report\Filament\Pages\CustomerAdvisorReport;
use AdvisingApp\Report\Filament\Pages\EmployeeAdvisorReport;
use AdvisingApp\Report\Filament\Pages\InstitutionalAdvisorReport;
use AdvisingApp\Report\Filament\Pages\ProspectInteractionReport;
use AdvisingApp\Report\Filament\Pages\ProspectMessagesDetailReport;
use AdvisingApp\Report\Filament\Pages\ProspectMessagesOverviewReport;
use AdvisingApp\Report\Filament\Pages\ProspectReport;
use AdvisingApp\Report\Filament\Pages\ProspectTaskManagement;
use AdvisingApp\Report\Filament\Pages\ResearchAdvisorReport;
use AdvisingApp\Report\Filament\Pages\StudentDeliverabilityReport;
use AdvisingApp\Report\Filament\Pages\StudentInteractionReport;
use AdvisingApp\Report\Filament\Pages\StudentMessagesDetailReport;
use AdvisingApp\Report\Filament\Pages\StudentMessagesOverviewReport;
use AdvisingApp\Report\Filament\Pages\Students;
use AdvisingApp\Report\Filament\Pages\StudentTaskManagement;
use AdvisingApp\Report\Filament\Pages\UserLoginActivity;
use AdvisingApp\Report\Models\ReportDepartmentAccess;
use AdvisingApp\Report\Models\ReportUserAccess;
use AdvisingApp\StudentDataModel\Filament\Pages\RetentionCrmDashboard;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Database\Eloquent\Builder;

enum ReportAccessKey: string
{
    case ArtificialIntelligence = 'artificial-intelligence';
    case CustomerAdvisorReport = 'customer-advisor-report';
    case EmployeeAdvisorReport = 'employee-advisor-report';
    case InstitutionalAdvisorReport = 'institutional-advisor-report';
    case ResearchAdvisorReport = 'research-advisor-report';
    case StudentActionCenter = 'student-action-center';
    case Students = 'students';
    case StudentDeliverabilityReport = 'student-deliverability-report';
    case StudentInteractionReport = 'student-interaction-report';
    case StudentMessagesDetailReport = 'student-messages-detail-report';
    case StudentMessagesOverviewReport = 'student-messages-overview-report';
    case StudentTaskManagement = 'student-task-management';
    case ProspectActionCenter = 'prospect-action-center';
    case ProspectReport = 'prospect-report';
    case ProspectInteractionReport = 'prospect-interaction-report';
    case ProspectMessagesDetailReport = 'prospect-messages-detail-report';
    case ProspectMessagesOverviewReport = 'prospect-messages-overview-report';
    case ProspectTaskManagement = 'prospect-task-management';
    case UserLoginActivity = 'user-login-activity';

    /**
     * @return class-string
     */
    public function getPageClass(): string
    {
        return match ($this) {
            self::ArtificialIntelligence => ArtificialIntelligence::class,
            self::CustomerAdvisorReport => CustomerAdvisorReport::class,
            self::EmployeeAdvisorReport => EmployeeAdvisorReport::class,
            self::InstitutionalAdvisorReport => InstitutionalAdvisorReport::class,
            self::ResearchAdvisorReport => ResearchAdvisorReport::class,
            self::StudentActionCenter => RetentionCrmDashboard::class,
            self::Students => Students::class,
            self::StudentDeliverabilityReport => StudentDeliverabilityReport::class,
            self::StudentInteractionReport => StudentInteractionReport::class,
            self::StudentMessagesDetailReport => StudentMessagesDetailReport::class,
            self::StudentMessagesOverviewReport => StudentMessagesOverviewReport::class,
            self::StudentTaskManagement => StudentTaskManagement::class,
            self::ProspectActionCenter => RecruitmentCrmDashboard::class,
            self::ProspectReport => ProspectReport::class,
            self::ProspectInteractionReport => ProspectInteractionReport::class,
            self::ProspectMessagesDetailReport => ProspectMessagesDetailReport::class,
            self::ProspectMessagesOverviewReport => ProspectMessagesOverviewReport::class,
            self::ProspectTaskManagement => ProspectTaskManagement::class,
            self::UserLoginActivity => UserLoginActivity::class,
        };
    }

    public function getName(): string
    {
        return match ($this) {
            self::ArtificialIntelligence => 'AI Utilization',
            self::CustomerAdvisorReport => 'Customer Advisor',
            self::EmployeeAdvisorReport => 'Employee Advisor',
            self::InstitutionalAdvisorReport => 'Institutional Advisor',
            self::ResearchAdvisorReport => 'Research Advisor',
            self::StudentActionCenter => 'Action Center (Students)',
            self::Students => 'Students (Overview)',
            self::StudentDeliverabilityReport => 'Deliverability (Students)',
            self::StudentInteractionReport => 'Interactions (Students)',
            self::StudentMessagesDetailReport => 'Messages Detail (Students)',
            self::StudentMessagesOverviewReport => 'Messages Overview (Students)',
            self::StudentTaskManagement => 'Tasks (Students)',
            self::ProspectActionCenter => 'Action Center (Prospects)',
            self::ProspectReport => 'Prospects (Overview)',
            self::ProspectInteractionReport => 'Interactions (Prospects)',
            self::ProspectMessagesDetailReport => 'Messages Detail (Prospects)',
            self::ProspectMessagesOverviewReport => 'Messages Overview (Prospects)',
            self::ProspectTaskManagement => 'Tasks (Prospects)',
            self::UserLoginActivity => 'Login Activity',
        };
    }

    public function getCategory(): string
    {
        return match ($this) {
            self::ArtificialIntelligence,
            self::CustomerAdvisorReport,
            self::EmployeeAdvisorReport,
            self::InstitutionalAdvisorReport,
            self::ResearchAdvisorReport => 'Enterprise AI',

            self::StudentActionCenter,
            self::Students,
            self::StudentDeliverabilityReport,
            self::StudentInteractionReport,
            self::StudentMessagesDetailReport,
            self::StudentMessagesOverviewReport,
            self::StudentTaskManagement => 'Students',

            self::ProspectActionCenter,
            self::ProspectReport,
            self::ProspectInteractionReport,
            self::ProspectMessagesDetailReport,
            self::ProspectMessagesOverviewReport,
            self::ProspectTaskManagement => 'Prospects',

            self::UserLoginActivity => 'Users',
        };
    }

    public function isAvailableForTenant(): bool
    {
        $addons = app(LicenseSettings::class)->data->addons;

        return match ($this) {
            self::StudentActionCenter,
            self::Students,
            self::StudentDeliverabilityReport,
            self::StudentInteractionReport,
            self::StudentMessagesDetailReport,
            self::StudentMessagesOverviewReport => LicenseType::RetentionCrm->isLicensable(),

            self::StudentTaskManagement => LicenseType::RetentionCrm->isLicensable() || LicenseType::RecruitmentCrm->isLicensable(),

            self::ProspectActionCenter,
            self::ProspectReport,
            self::ProspectInteractionReport,
            self::ProspectMessagesDetailReport,
            self::ProspectMessagesOverviewReport => LicenseType::RecruitmentCrm->isLicensable(),

            self::ProspectTaskManagement => LicenseType::RecruitmentCrm->isLicensable(),

            self::ArtificialIntelligence,
            self::InstitutionalAdvisorReport => LicenseType::ConversationalAi->isLicensable(),

            self::CustomerAdvisorReport => LicenseType::ConversationalAi->isLicensable() && $addons->customerAdvisors,

            self::EmployeeAdvisorReport => LicenseType::ConversationalAi->isLicensable() && $addons->employeeAdvisors,

            self::ResearchAdvisorReport => LicenseType::ConversationalAi->isLicensable() && $addons->researchAdvisor,

            self::UserLoginActivity => true,
        };
    }

    public static function fromPageClass(string $pageClass): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->getPageClass() === $pageClass) {
                return $case;
            }
        }

        return null;
    }

    public function userCanAccess(User $user): bool
    {
        if ($user->isSuperAdmin() || $user->isPartnerAdmin()) {
            return true;
        }

        return ReportUserAccess::query()
            ->where('report_key', $this->value)
            ->where('user_id', $user->getKey())
            ->selectRaw('1')
            ->union(
                ReportDepartmentAccess::query()
                    ->where('report_key', $this->value)
                    ->where('team_id', $user->team_id)
                    ->selectRaw('1')
            )
            ->exists();
    }

    /**
     * The number of distinct users that have access to the report, counting both
     * direct user assignments and members of assigned departments (deduplicated).
     */
    public function accessCount(): int
    {
        return User::query()
            ->where(function (Builder $query) {
                $query->whereIn(
                    'id',
                    ReportUserAccess::query()
                        ->where('report_key', $this->value)
                        ->select('user_id')
                )
                    ->orWhereIn(
                        'team_id',
                        ReportDepartmentAccess::query()
                            ->where('report_key', $this->value)
                            ->select('team_id')
                    );
            })
            ->count();
    }
}
