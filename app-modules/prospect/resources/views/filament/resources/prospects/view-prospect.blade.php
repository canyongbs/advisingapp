{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.
    
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
--}}
@php
    use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableActivityFeedWidget;
    use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableConcernsWidget;
    use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableCareTeamWidget;
    use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableSubscriptionsWidget;
    use AdvisingApp\StudentDataModel\Filament\Resources\Educatables\Widgets\EducatableTasksWidget;
    use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
    use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\ApplicationSubmissionsRelationManager;
    use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\EngagementFilesRelationManager;
    use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\EngagementsRelationManager;
    use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\EventsRelationManager;
    use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\FormSubmissionsRelationManager;
    use AdvisingApp\StudentDataModel\Filament\Resources\Students\RelationManagers\InteractionsRelationManager;
    use AdvisingApp\Prospect\Filament\Resources\Prospects\RelationManagers\CasesRelationManager;
@endphp

<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-16 xl:grid-cols-3">
        <div class="col-span-1">
            <div class="grid gap-8">
                {{ $this->profile }}

                @if (EducatableActivityFeedWidget::canViewForRecord($this->getRecord()))
                    @livewire(
                        EducatableActivityFeedWidget::class,
                        [
                            'educatable' => $this->getRecord(),
                            'lazy' => 'on-load',
                            'viewUrl' => ProspectResource::getUrl('activity-feed', ['record' => $this->getRecord()]),
                        ]
                    )
                @endif
            </div>
        </div>

        <div class="flex flex-col gap-8 lg:col-span-1 xl:col-span-2">
            <x-student-data-model::filament.resources.educatables.view-educatable.relation-managers
                :managers="[
                    'messages' => EngagementsRelationManager::class,
                    'interactions' => InteractionsRelationManager::class,
                    'cases' => CasesRelationManager::class,
                    'files' => EngagementFilesRelationManager::class,
                ]"
            />

            <div class="grid grid-cols-1 gap-8 xl:grid-cols-2">
                @if (EducatableConcernsWidget::canView())
                    @livewire(
                        EducatableConcernsWidget::class,
                        [
                            'educatable' => $this->getRecord(),
                            'resource' => ProspectResource::class,
                        ]
                    )
                @endif

                @if (EducatableTasksWidget::canView())
                    @livewire(
                        EducatableTasksWidget::class,
                        [
                            'educatable' => $this->getRecord(),
                            'resource' => ProspectResource::class,
                        ]
                    )
                @endif
            </div>

            <div class="grid grid-cols-1 gap-8 xl:grid-cols-2">
                @if (EducatableCareTeamWidget::canView())
                    @livewire(
                        EducatableCareTeamWidget::class,
                        [
                            'educatable' => $this->getRecord(),
                            'lazy' => 'on-load',
                            'manageUrl' => ProspectResource::getUrl('care-team', ['record' => $this->getRecord()]),
                        ]
                    )
                @endif

                @if (EducatableSubscriptionsWidget::canView())
                    @livewire(
                        EducatableSubscriptionsWidget::class,
                        [
                            'educatable' => $this->getRecord(),
                            'lazy' => 'on-load',
                            'manageUrl' => ProspectResource::getUrl('manage-subscriptions', ['record' => $this->getRecord()]),
                        ]
                    )
                @endif
            </div>

            <x-student-data-model::filament.resources.educatables.view-educatable.relation-managers
                :managers="[
                    'forms' => FormSubmissionsRelationManager::class,
                    'events' => EventsRelationManager::class,
                    'applications' => ApplicationSubmissionsRelationManager::class,
                ]"
            />
        </div>
    </div>

    @if (! app()->hasDebugModeEnabled())
        @script
            <script>
                Livewire.hook('request', ({ fail }) => {
                    fail(({ status, content, preventDefault }) => {
                        preventDefault();

                        new FilamentNotification()
                            .title('Error while loading page')
                            .body(
                                'There was an error rendering some information on the holistic prospect profile page. We are tracking this error on the back end and will work on getting this fixed.',
                            )
                            .danger()
                            .send();
                    });
                });
            </script>
        @endscript
    @endif
</x-filament-panels::page>
