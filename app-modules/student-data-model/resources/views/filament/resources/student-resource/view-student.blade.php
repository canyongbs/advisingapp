{{--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
    use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;
@endphp
<x-student-data-model::page @class([
    'fi-resource-view-record-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    'fi-resource-record-' . $record->getKey(),
])>
    <x-student-data-model::student-header-section :record="$record" />
    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-8">
            <x-student-data-model::student-profile-section :record="$record" />
            <livewire:student-data-model::student-engagement-timeline :record="$record->getKey()" />
        </div>
        <div class="col-auto lg:col-span-2">
            <div class="flex flex-col gap-8">
                <livewire:student-data-model::manage-student-information :record="$record->getKey()" />
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <livewire:student-alert-relation-manager
                        :owner-record="$record"
                        :page-class="ViewStudent::class"
                    />

                    <livewire:student-data-model::student-tasks-relation-manager
                        :owner-record="$record"
                        :page-class="ViewStudent::class"
                    />
                    <livewire:student-care-team-relation-manager
                        :owner-record="$record"
                        :page-class="ViewStudent::class"
                    />
                    <livewire:student-subscriptions-relation-manager
                        :owner-record="$record"
                        :page-class="ViewStudent::class"
                    />
                </div>

                <livewire:student-data-model::manage-student-premium-features :record="$record->getKey()" />
            </div>
        </div>
    </div>
</x-student-data-model::page>
