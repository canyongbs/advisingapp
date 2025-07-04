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

namespace AdvisingApp\CaseManagement\Providers;

use AdvisingApp\CaseManagement\CaseManagementPlugin;
use AdvisingApp\CaseManagement\Cases\CaseNumber\Contracts\CaseNumberGenerator;
use AdvisingApp\CaseManagement\Cases\CaseNumber\SqidPlusSixCaseNumberGenerator;
use AdvisingApp\CaseManagement\Models\CaseAssignment;
use AdvisingApp\CaseManagement\Models\CaseForm;
use AdvisingApp\CaseManagement\Models\CaseFormAuthentication;
use AdvisingApp\CaseManagement\Models\CaseFormField;
use AdvisingApp\CaseManagement\Models\CaseFormStep;
use AdvisingApp\CaseManagement\Models\CaseFormSubmission;
use AdvisingApp\CaseManagement\Models\CaseHistory;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Models\CaseUpdate;
use AdvisingApp\CaseManagement\Models\Sla;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class CaseManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new CaseManagementPlugin()));

        $this->app->bind(CaseNumberGenerator::class, SqidPlusSixCaseNumberGenerator::class);
    }

    public function boot(): void
    {
        Relation::morphMap([
            'case_assignment' => CaseAssignment::class,
            'case_form_authentication' => CaseFormAuthentication::class,
            'case_form_field' => CaseFormField::class,
            'case_form_step' => CaseFormStep::class,
            'case_form_submission' => CaseFormSubmission::class,
            'case_form' => CaseForm::class,
            'case_history' => CaseHistory::class,
            'case_priority' => CasePriority::class,
            'case_status' => CaseStatus::class,
            'case_type' => CaseType::class,
            'case_update' => CaseUpdate::class,
            'case_model' => CaseModel::class,
            'sla' => Sla::class,
        ]);
    }
}
