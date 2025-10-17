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

namespace AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets;

use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Alert\Models\AlertStatus;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

class EducatableAlertsWidget extends Widget
{
    protected string $view = 'student-data-model::filament.resources.educatable-resource.widgets.educatable-alerts-widget';

    #[Locked]
    public Educatable&Model $educatable;

    #[Locked]
    public string $resource;

    public static function canView(): bool
    {
        return auth()->user()->can('viewAny', Alert::class);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getStatusCounts(): array
    {
        $counts = $this->educatable->alerts()
            ->select('alert_statuses.classification')
            ->selectRaw('count(*) as alert_count')
            ->join('alert_statuses', 'alerts.status_id', '=', 'alert_statuses.id')
            ->groupBy('alert_statuses.classification')
            ->get();

        return collect(SystemAlertStatusClassification::cases())
            ->map(fn (SystemAlertStatusClassification $classification) => [
                'id' => AlertStatus::where('classification', $classification->value)->first()?->id,
                'classification' => $classification->value,
                'alert_count' => $counts->where('classification', $classification->value)->first()->alert_count ?? 0,
            ])->toArray();
    }

    /**
     * @param array<string, mixed> $filters
    */
    protected function getAlertsUrl(array $filters = []): string
    {
        return $this->resource::getUrl('alerts', ['record' => $this->educatable, 'filters' => $filters]);
    }
}
