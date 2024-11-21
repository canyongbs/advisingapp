<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\Locked;
use Illuminate\Database\Eloquent\Model;
use AdvisingApp\Alert\Enums\AlertStatus;
use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;

class EducatableAlertsWidget extends Widget
{
    protected static string $view = 'student-data-model::filament.resources.educatable-resource.widgets.educatable-alerts-widget';

    #[Locked]
    public Educatable&Model $educatable;

    #[Locked]
    public string $manageUrl;

    public static function canView(): bool
    {
        return auth()->user()->can('alert.view-any');
    }

    protected function getActiveCount(): int
    {
        return $this->educatable->alerts()
            ->where('status', AlertStatus::Active)
            ->count();
    }

    protected function getSeverityCounts(): array
    {
        $counts = $this->educatable->alerts()
            ->toBase()
            ->selectRaw('count(*) as alert_count, severity')
            ->groupBy('severity')
            ->pluck('alert_count', 'severity');

        return collect(AlertSeverity::cases())
            ->reverse()
            ->mapWithKeys(fn (AlertSeverity $alertSeverity): array => [$alertSeverity->getLabel() => $counts[$alertSeverity->value] ?? 0])
            ->filter()
            ->all();
    }
}
