{{--
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
--}}
@php
    use AdvisingApp\Assistant\Filament\Pages\PersonalAssistant;
    use AdvisingApp\Authorization\Enums\LicenseType;
    use AdvisingApp\Prospect\Filament\Pages\RecruitmentCrmDashboard;
    use AdvisingApp\StudentDataModel\Filament\Pages\RetentionCrmDashboard;
@endphp

<x-filament-widgets::widget>
    <div class="grid gap-6 md:grid-cols-3">
        @php
            $hasFeature = auth()
                ->user()
                ->hasLicense(LicenseType::RecruitmentCrm);
        @endphp
        <x-filament::section @class([
            'opacity-50 pointer-events-none' => !$hasFeature,
        ])>
            <div class="flex flex-col gap-3">
                <div class="text-center text-lg font-bold">
                    {{ $hasFeature ? 'Available' : 'Unavailable' }}
                </div>

                <div class="flex items-center justify-center">
                    <x-filament::button
                        :href="$hasFeature ? RecruitmentCrmDashboard::getUrl() : null"
                        size="xl"
                        tag="a"
                        color="gray"
                    >
                        Start now
                    </x-filament::button>
                </div>

                <div class="text-center font-medium text-gray-700 dark:text-gray-300">
                    Recruitment CRM
                </div>
            </div>
        </x-filament::section>

        @php
            $hasFeature = auth()
                ->user()
                ->hasLicense(LicenseType::RetentionCrm);
        @endphp
        <x-filament::section @class([
            'opacity-50 pointer-events-none' => !$hasFeature,
        ])>
            <div class="flex flex-col gap-3">
                <div class="text-center text-lg font-bold">
                    {{ $hasFeature ? 'Available' : 'Unavailable' }}
                </div>

                <div class="flex items-center justify-center">
                    <x-filament::button
                        :href="$hasFeature ? RetentionCrmDashboard::getUrl() : null"
                        size="xl"
                        tag="a"
                        color="gray"
                    >
                        Start now
                    </x-filament::button>
                </div>

                <div class="text-center font-medium text-gray-700 dark:text-gray-300">
                    Student Success Suite
                </div>
            </div>
        </x-filament::section>

        @php
            $hasFeature = auth()
                ->user()
                ->hasLicense(LicenseType::ConversationalAi);
        @endphp
        <x-filament::section @class([
            'opacity-50 pointer-events-none' => !$hasFeature,
        ])>
            <div class="flex flex-col gap-3">
                <div class="text-center text-lg font-bold">
                    {{ $hasFeature ? 'Available' : 'Unavailable' }}
                </div>

                <div class="flex items-center justify-center">
                    <x-filament::button
                        :href="$hasFeature ? PersonalAssistant::getUrl() : null"
                        size="xl"
                        tag="a"
                        color="gray"
                    >
                        Start now
                    </x-filament::button>
                </div>

                <div class="text-center font-medium text-gray-700 dark:text-gray-300">
                    Enterprise AI Assistant
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-widgets::widget>
