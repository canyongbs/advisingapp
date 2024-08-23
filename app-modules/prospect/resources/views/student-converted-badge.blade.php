@use('AdvisingApp\StudentDataModel\Filament\Resources\StudentResource')
@if(Feature::active('convert_prospect_to_student') && $this->getRecord()->student)
    <x-filament::badge size="md" color="success" class="-mb-4 mt-3 px-3 py-3">
        <span>
            This record has been merged with a student record.
            <a
                class="underline"
                href="{{ StudentResource::getUrl('view', ['record' => $this->getRecord()?->student]) }}"
            >Click here</a> to visit the student record.
        </span>
    </x-filament::badge>
@endif