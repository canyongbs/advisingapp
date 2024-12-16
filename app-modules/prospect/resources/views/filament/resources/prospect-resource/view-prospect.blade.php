@php
    use AdvisingApp\Prospect\Filament\Resources\EducatableResource\Widgets\EducatableActivityFeedWidget;
    use AdvisingApp\Prospect\Filament\Resources\EducatableResource\Widgets\EducatableAlertsWidget;
    use AdvisingApp\Prospect\Filament\Resources\EducatableResource\Widgets\EducatableCareTeamWidget;
    use AdvisingApp\Prospect\Filament\Resources\EducatableResource\Widgets\EducatableSubscriptionsWidget;
    use AdvisingApp\Prospect\Filament\Resources\EducatableResource\Widgets\EducatableTasksWidget;
    use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
    use AdvisingApp\Prospect\Filament\Resources\StudentResource\RelationManagers\ApplicationSubmissionsRelationManager;
    use AdvisingApp\Prospect\Filament\Resources\StudentResource\RelationManagers\EngagementFilesRelationManager;
    use AdvisingApp\Prospect\Filament\Resources\StudentResource\RelationManagers\EngagementsRelationManager;
    use AdvisingApp\Prospect\Filament\Resources\StudentResource\RelationManagers\EnrollmentsRelationManager;
    use AdvisingApp\Prospect\Filament\Resources\StudentResource\RelationManagers\EventsRelationManager;
    use AdvisingApp\Prospect\Filament\Resources\StudentResource\RelationManagers\FormSubmissionsRelationManager;
    use AdvisingApp\Prospect\Filament\Resources\StudentResource\RelationManagers\InteractionsRelationManager;
    use AdvisingApp\Prospect\Filament\Resources\StudentResource\RelationManagers\ProgramsRelationManager;
@endphp

<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-16 xl:grid-cols-3">
        <div class="col-span-1">
            <div class="grid gap-8">
                {{ $this->profile }}

                {{-- @if (EducatableActivityFeedWidget::canView())
                    @livewire(EducatableActivityFeedWidget::class, [
                        'educatable' => $this->getRecord(),
                        'lazy' => 'on-load',
                        'viewUrl' => ProspectResource::getUrl('activity-feed', ['record' => $this->getRecord()]),
                    ])
                @endif --}}
            </div>
        </div>

        {{-- <div class="flex flex-col gap-8 lg:col-span-1 xl:col-span-2">
            <x-student-data-model::filament.resources.educatable-resource.view-educatable.relation-managers
                :managers="[
                    'programs' => ProgramsRelationManager::class,
                    'enrollments' => EnrollmentsRelationManager::class,
                    'messages' => EngagementsRelationManager::class,
                    'interactions' => InteractionsRelationManager::class,
                    'files' => EngagementFilesRelationManager::class,
                ]"
            />

            <div class="grid grid-cols-1 gap-8 xl:grid-cols-2">
                @if (EducatableAlertsWidget::canView())
                    @livewire(EducatableAlertsWidget::class, [
                        'educatable' => $this->getRecord(),
                        'manageUrl' => StudentResource::getUrl('alerts', ['record' => $this->getRecord()]),
                    ])
                @endif

                @if (EducatableTasksWidget::canView())
                    @livewire(EducatableTasksWidget::class, [
                        'educatable' => $this->getRecord(),
                        'manageUrl' => StudentResource::getUrl('tasks', ['record' => $this->getRecord()]),
                    ])
                @endif
            </div>

            <div class="grid grid-cols-1 gap-8 xl:grid-cols-2">
                @if (EducatableCareTeamWidget::canView())
                    @livewire(EducatableCareTeamWidget::class, [
                        'educatable' => $this->getRecord(),
                        'lazy' => 'on-load',
                        'manageUrl' => StudentResource::getUrl('care-team', ['record' => $this->getRecord()]),
                    ])
                @endif

                @if (EducatableSubscriptionsWidget::canView())
                    @livewire(EducatableSubscriptionsWidget::class, [
                        'educatable' => $this->getRecord(),
                        'lazy' => 'on-load',
                        'manageUrl' => StudentResource::getUrl('subscriptions', ['record' => $this->getRecord()]),
                    ])
                @endif
            </div>

            <x-student-data-model::filament.resources.educatable-resource.view-educatable.relation-managers
                :managers="[
                    'forms' => FormSubmissionsRelationManager::class,
                    'events' => EventsRelationManager::class,
                    'applications' => ApplicationSubmissionsRelationManager::class,
                ]"
            />
        </div> --}}
    </div>
</x-filament-panels::page>
