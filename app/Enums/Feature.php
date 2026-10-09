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

namespace App\Enums;

use AdvisingApp\Ai\Filament\Exports\AssistantUtilizationExporter;
use AdvisingApp\Ai\Filament\Exports\CustomerAdvisorCategoryExporter;
use AdvisingApp\Ai\Filament\Exports\CustomerAdvisorQuestionExporter;
use AdvisingApp\Ai\Filament\Exports\EmployeeAdvisorCategoryExporter;
use AdvisingApp\Ai\Filament\Exports\EmployeeAdvisorQuestionExporter;
use AdvisingApp\Ai\Filament\Imports\CustomerAdvisorCategoryImporter;
use AdvisingApp\Ai\Filament\Imports\CustomerAdvisorQuestionImporter;
use AdvisingApp\Ai\Filament\Imports\EmployeeAdvisorCategoryImporter;
use AdvisingApp\Ai\Filament\Imports\EmployeeAdvisorQuestionImporter;
use App\Models\Authenticatable;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Gate;

enum Feature: string
{
    case OnlineForms = 'online-forms';

    case OnlineSurveys = 'online-surveys';

    case OnlineAdmissions = 'online-admissions';

    case ResourceHub = 'resource-hub';

    case SupportPrograms = 'support-programs';

    case EventManagement = 'event-management';

    case ScheduleAndAppointments = 'schedule-and-appointments';

    case EmployeeAdvisors = 'employee-advisors';

    case CustomerAdvisors = 'customer-advisors';

    case EarlyAlert = 'early-alert';

    case PublicProfiles = 'public-profiles';

    case EnterpriseAi = 'enterprise-ai';

    case UnifiedInbox = 'unified-inbox';

    public function generateGate(): void
    {
        // If features are added that are not based on a License Addon we will need to update this
        Gate::define(
            $this->getGateName(),
            fn (?Authenticatable $authenticatable) => app(LicenseSettings::class)->data->addons->{str($this->value)->camel()}
        );
    }

    public function getGateName(): string
    {
        return "feature-{$this->value}";
    }

    /**
     * @return array<string>
     */
    public function getPermissionGroupNames(): array
    {
        return match ($this) {
            Feature::EnterpriseAi => [
                'Assistant',
                'Assistant Chat Message Log',
                'Assistant Custom',
                'Customer Advisor',
                'Customer Advisor Embed',
                'Prompt',
            ],
            default => [],
        };
    }

    /**
     * The importers and exporters that belong to this feature.
     * Their imports and exports are hidden from the Import/Export page, and cannot be downloaded, while the feature is disabled.
     *
     * @return array<class-string>
     */
    public function getImporterAndExporterClasses(): array
    {
        return match ($this) {
            Feature::EnterpriseAi => [
                CustomerAdvisorCategoryImporter::class,
                CustomerAdvisorQuestionImporter::class,
                EmployeeAdvisorCategoryImporter::class,
                EmployeeAdvisorQuestionImporter::class,
                AssistantUtilizationExporter::class,
                CustomerAdvisorCategoryExporter::class,
                CustomerAdvisorQuestionExporter::class,
                EmployeeAdvisorCategoryExporter::class,
                EmployeeAdvisorQuestionExporter::class,
            ],
            default => [],
        };
    }

    /**
     * @return array<class-string>
     */
    public static function getDisabledImporterAndExporterClasses(): array
    {
        return collect(Feature::cases())
            ->reject(fn (Feature $feature): bool => Gate::check($feature->getGateName()))
            ->flatMap(fn (Feature $feature): array => $feature->getImporterAndExporterClasses())
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<string>
     */
    public static function getDisabledPermissionGroupNames(): array
    {
        return collect(Feature::cases())
            ->reject(fn (Feature $feature): bool => Gate::check($feature->getGateName()))
            ->flatMap(fn (Feature $feature): array => $feature->getPermissionGroupNames())
            ->unique()
            ->values()
            ->all();
    }
}
