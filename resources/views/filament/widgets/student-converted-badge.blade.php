@use('AdvisingApp\StudentDataModel\Filament\Resources\StudentResource')

<div
    class="-mb-3 inline-block w-full rounded-lg border border-green-500 p-3 text-xs font-medium text-success-400 dark:bg-success-400/10">
    <span>
        This record has been merged with a student record.
        <a
            class="underline"
            href="{{ StudentResource::getUrl('view', ['record' => $this->student]) }}"
        >Click here</a> to visit the student record.
    </span>
</div>
