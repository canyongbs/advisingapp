@php
    use AdvisingApp\StudentDataModel\Livewire\StudentDataImportsTable;
    use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
@endphp

<x-filament-panels::page>
    @if (app(ManageStudentConfigurationSettings::class)->is_enabled)
        @livewire(StudentDataImportsTable::class)
    @endif
</x-filament-panels::page>