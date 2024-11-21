<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\Locked;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;

class EducatableSubscriptionsWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected static string $view = 'student-data-model::filament.resources.educatable-resource.widgets.educatable-subscriptions-widget';

    #[Locked]
    public Educatable&Model $educatable;

    public static function canView(): bool
    {
        return auth()->user()->can('subscription.view-any');
    }

    protected function getSubscribedUsers(): array
    {
        return $this->educatable->subscribedUsers()
            ->orderByDesc('subscriptions.created_at')
            ->get()
            ->all();
    }
}
