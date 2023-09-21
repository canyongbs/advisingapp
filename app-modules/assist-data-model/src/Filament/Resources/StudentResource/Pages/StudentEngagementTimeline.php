<?php

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Assist\Engagement\Models\Engagement;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Models\EngagementResponse;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Engagement\Filament\Resources\EngagementResource\Pages\EngagementTimeline;

class StudentEngagementTimeline extends EngagementTimeline
{
    protected static string $resource = StudentResource::class;

    public Student $student;

    public function mount($record): void
    {
        $this->student = $this->record = $this->resolveRecord($record);

        // TODO Authorization
        $this->authorizeAccess();

        $this->aggregateEngagements = $this->aggregateEngagements();
    }

    public function aggregateEngagements(): Collection
    {
        $this->aggregateEngagements = collect();

        // TODO Extract this logic to a trait that can be shared across models/resources that are going to use this timeline
        $engagements = $this->student->engagements()
            ->with(['deliverables', 'batch'])
            ->get();

        $engagementResponses = $this->student->engagementResponses()
            ->get();

        $this->aggregateEngagements = $engagements->concat($engagementResponses);

        $this->aggregateEngagements = $this->aggregateEngagements->sortByDesc(function (Engagement|EngagementResponse $record) {
            if ($record instanceof Engagement) {
                return Carbon::parse($record->deliver_at)->timestamp;
            }

            if ($record instanceof EngagementResponse) {
                return Carbon::parse($record->sent_at)->timestamp;
            }

            return null;
        });

        return $this->aggregateEngagements;
    }
}
