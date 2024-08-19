<?php
    use AdvisingApp\StudentDataModel\Models\Student;
    use Illuminate\Support\Carbon;
    use App\Settings\DisplaySettings;

    /** @var Student $student */ 

    $timezone = app(DisplaySettings::class)->getTimezone();
?>
<div class="flex flex-col items-center md:flex-row gap-3">
    <div class="flex-1">
        <p class="text-xs">
            This record was last updated in the SIS on {{ $student->updated_at_source->setTimezone($timezone)->format('F j, Y g:i A') }}.
        </p>
    </div>

    <div class="flex-shrink-0">

        <x-filament::button
            type="button"
            color="gray"
            icon="heroicon-m-arrow-path"
            labeled-from="sm"
            tag="button"
            wire:click="sisRefresh()"
        >
            {{ 'Sync' }}
        </x-filament::button>

    </div>
</div>