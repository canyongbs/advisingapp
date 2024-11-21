<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\Locked;
use Illuminate\Database\Eloquent\Model;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;

class EducatableCareTeamWidget extends Widget
{
    protected static string $view = 'student-data-model::filament.resources.educatable-resource.widgets.educatable-care-team-widget';

    #[Locked]
    public Educatable&Model $educatable;

    #[Locked]
    public string $manageUrl;

    public static function canView(): bool
    {
        return auth()->user()->can('care_team.view-any');
    }

    protected function getCareTeam(): array
    {
        return $this->educatable->careTeam()
            ->orderBy('care_teams.created_at')
            ->get()
            ->all();
    }
}
