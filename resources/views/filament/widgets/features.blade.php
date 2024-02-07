@php
    use AdvisingApp\Assistant\Filament\Pages\PersonalAssistant;
    use AdvisingApp\Authorization\Enums\LicenseType;
    use AdvisingApp\Prospect\Filament\Pages\RecruitmentCrmDashboard;
    use AdvisingApp\StudentDataModel\Filament\Pages\RetentionCrmDashboard;
@endphp

<x-filament-widgets::widget>
    <div class="grid md:grid-cols-3 gap-6">
        @php
            $hasFeature = auth()->user()->hasLicense(LicenseType::RecruitmentCrm);
        @endphp
        <x-filament::section @class([
            'opacity-50 pointer-events-none' => ! $hasFeature,
        ])>
            <div class="flex flex-col gap-3">
                <div class="text-lg font-bold text-center">
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

                <div class="font-medium text-gray-700 text-center dark:text-gray-300">
                    Recruitment CRM
                </div>
            </div>
        </x-filament::section>

        @php
            $hasFeature = auth()->user()->hasLicense(LicenseType::RetentionCrm);
        @endphp
        <x-filament::section @class([
            'opacity-50 pointer-events-none' => ! $hasFeature,
        ])>
            <div class="flex flex-col gap-3">
                <div class="text-lg font-bold text-center">
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

                <div class="font-medium text-gray-700 text-center dark:text-gray-300">
                    Student Success Suite
                </div>
            </div>
        </x-filament::section>

        @php
            $hasFeature = auth()->user()->hasLicense(LicenseType::ConversationalAi);
        @endphp
        <x-filament::section @class([
            'opacity-50 pointer-events-none' => ! $hasFeature,
        ])>
            <div class="flex flex-col gap-3">
                <div class="text-lg font-bold text-center">
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

                <div class="font-medium text-gray-700 text-center dark:text-gray-300">
                    Enterprise AI Assistant
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-widgets::widget>
