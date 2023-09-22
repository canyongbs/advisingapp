<?php

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Engagement\Filament\Resources\EngagementResource\Pages\Timeline;

class StudentEngagementTimeline extends Timeline
{
    protected static string $resource = StudentResource::class;

    public string $emptyStateMessage = 'There are no engagements to show.';

    // TODO Implement this
    public array $modelsToTimeline = [
        Engagement::class,
        EngagementResponse::class,
    ];

    public Student $student;

    public function mount($record): void
    {
        $this->student = $this->record = $this->resolveRecord($record);

        // TODO Authorization
        $this->authorizeAccess();

        $this->aggregateRecords = $this->aggregateRecords();
    }

    public function aggregateRecords(): Collection
    {
        $this->aggregateRecords = collect();

        // TODO Now we need some sort of way to find the connection
        // Between the record and the models being timelined
        // We can use reflection in order to easily find the relationships
        // To call from the morph label of the class

        $engagements = $this->student->engagements()
            ->with(['deliverables', 'batch'])
            ->get();

        $engagementResponses = $this->student->engagementResponses()
            ->get();

        $this->aggregateRecords = $engagements->concat($engagementResponses);

        return $this->aggregateRecords = $this->aggregateRecords->sortByDesc(function (Engagement|EngagementResponse $record) {
            // TODO This field needs to be defined on a timelineable model
            // So that we don't need to do different checks here
            return Carbon::parse($record->sortableBy())->timestamp;
        });
    }
}
