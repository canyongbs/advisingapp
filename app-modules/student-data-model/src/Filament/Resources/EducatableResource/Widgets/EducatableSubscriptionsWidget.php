<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\Locked;
use Illuminate\Database\Eloquent\Model;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;

class EducatableSubscriptionsWidget extends Widget
{
    protected static string $view = 'student-data-model::filament.resources.educatable-resource.widgets.educatable-subscriptions-widget';

    #[Locked]
    public Educatable&Model $educatable;

    #[Locked]
    public string $manageUrl;

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
